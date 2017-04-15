<?php
class Sync{

  public static function update_after_offset($offset,$local_db,$shared_db,$my_fed){
    $str="select * from lo_update_log where id > $offset";
    $query=$local_db->query($str);
    while($row=mysqli_fetch_array($query)){
      /*
        checking if the current row (which will be updated) is a draft, if it is,
        delete the commit and don't send it to the shared.db, but keep the row in local.db
      */

      $str="select * from lo_general as G inner join lo_lifecycle as L using(Id_Lo,Id_Fd) where G.Id_Fd like '$my_fed' and G.id=".$row["local_id"];

      $r = $local_db->query($str);
      $r = mysqli_fetch_array($r);

      if($r["Status"]=="draft" || $r["Id_Fd"] != $my_fed){
        $statement = $local_db->prepare("delete from lo_update_log where id=?");
        $statement->bind_param("i",$row["id"]);
        $statement->execute();
        $statement->close();
      }else{
        //else update

        //deleting the update row log, indicating this row has been updated to the shared_db
        $statement = $local_db->prepare("delete from lo_update_log where id = ?");
        $statement->bind_param("i",$row["id"]);
        if ($statement->execute() == false) echo "\n\t\tERROR: ".$statement->error;
        $statement->close();

        //fetching the row from general
        $result=$local_db->query("select * from lo_general where Id_Fd like '$my_fed' and id = ".$row["local_id"]);

        $row=mysqli_fetch_array($result);

        /*
          This will make sure that if an article with the same Id_Fd and the same id already exists
          in the shared.db, it will be deleted before inserting the new, updated, version of that article.
          This avoid duplicate entries and simulates and update query, and since the id of the row itself
          is auto_incremented, this means that every daemon listening to the given shared.db will be notified
          when the next routine starts, and they will download the new updated, article.
          The other daemons must make sure they also delete the old version of the entry bofore inserting the new one.
        */


        $statement = $shared_db->prepare("delete from lo_general where Id_Fd like ? and Id_Lo = ?");
        $statement->bind_param("si",$my_fed,$row["id"]);
        if ($statement->execute() == false) echo "\n\t\tERROR: ".$statement->error;

        $statement->close();


        /*
          Now that I'm sure there are no duplicates,
          I can upload the entry to the shared.db
        */
        $str="insert into lo_general(
            Id_Fd,Id_Lo,
            Title,Language,
            Description,Keyword,Coverage,
            Structure,Aggregation_Level
          ) "
            ."values (?,?,?,?,?,?,?,?,?)";
        $statement = $shared_db->prepare($str);
        $statement->bind_param("sissssssi",
          $row["Id_Fd"],$row["id"],
          $row["Title"],$row["Language"],
          $row["Description"],$row["Keyword"],$row["Coverage"],
          $row["Structure"],$row["Aggregation_Level"]
        );
        $statement->execute();
        $statement->close();

        /*
          there's actually no need to insert it right away,
          the other daemons can download the newest version durin the next sync routine
        */

        echo "\n\t\t>>Row ".$row["id"]." has been updated.";
      }
    }
  }

  public static function update_all($local_db,$shared_db,$my_fed){
    Sync::update_after_offset(0,$local_db,$shared_db,$my_fed);
  }


  //uploads data from left database (starting from row $offset) to right database
  public static function upload_after_offset($offset,$local_db,$shared_db,$my_fed){

      $str="select * from lo_general as G inner join lo_lifecycle as L using(Id_Lo,Id_Fd) where G.id>$offset";
      $result=$local_db->query($str);
      $drafts_counter=0;
      while($row=mysqli_fetch_array($result)){

        if($row["Status"]!='draft'){
          $str="insert into lo_general(
              Id_Fd,Id_Lo,
              Title,Language,
              Description,Keyword,Coverage,
              Structure,Aggregation_Level
            ) "
              ."values (?,?,?,?,?,?,?,?,?)";
          $statement = $shared_db->prepare($str);
          $statement->bind_param("sissssssi",
            $row["Id_Fd"],$row["id"],
            $row["Title"],$row["Language"],
            $row["Description"],$row["Keyword"],$row["Coverage"],
            $row["Structure"],$row["Aggregation_Level"]
          );
          $statement->execute();
          $tmp_insert_id=$statement->insert_id;
          $statement->close();
          echo "\n\t\t>>Row ".$row["id"]." has been uploaded.";


        }else{
          $drafts_counter++;
        }

      }

      if($drafts_counter>0){
        echo "\n\t\t$drafts_counter object";
        if($drafts_counter>1){
          echo "s are drafts";
        }else{
          echo " is draft";
        }
        echo" and will not be uploaded.";
      }
  }



  //uploads data from left database to right database
  public static function upload_all($local_db,$shared_db,$my_fed){
    Sync::upload_after_offset(0,$local_db,$shared_db,$my_fed);
  }


  public static function download_after_offset($offset,$shared_db,$local_db,$my_fed){
    $query=$shared_db->query("select * from lo_general where Id_Fd not like '$my_fed' AND id > $offset");
    while($row=mysqli_fetch_array($query)){
      /*
        Deleting duplicate entries and writing a new one using the
        same IDs but with updated data (simulates an update query)
      */
      $statement=$local_db->prepare("delete from lo_general where Id_Fd like ? and Id_Lo = ?");
      $statement->bind_param("si",$my_fed,$row["Id_Lo"]);
      if($statement->execute() == false) echo "\n\t\tERROR:".$statement->error;
      $statement->close();


      $str="insert into lo_general(
          Id_Fd,Id_Lo,shared_id,
          Status,Title,Language,
          Description,Keyword,Coverage,
          Structure,Aggregation_Level
        ) "
          ."values (?,?,?,?,?,?,?,?,?,?,?)";

      $statement = $local_db->prepare($str);
      $statement->bind_param("siisssssssi",
        $row["Id_Fd"],$row["Id_Lo"],$row["id"],
        $row["Status"],$row["Title"],$row["Language"],
        $row["Description"],$row["Keyword"],$row["Coverage"],
        $row["Structure"],$row["Aggregation_Level"]
      );
      if($statement->execute() == false) echo "\n\t\tERROR: ".$statement->error;
      $tmp_insert_id = $statement->insert_id;
      $statement->close();


      echo "\n\t\t<<Row ".$row["id"]." has been downloaded.";
    }
  }

  public static function download_all($shared_db,$local_db,$my_fed){
    $this->download_after_offset(0,$shared_db,$local_db,$my_fed);
  }


  public static function get_last_update_log($local_db){
    $str="select * from lo_update_log order by id desc limit 1";
    $query=$local_db->query($str);

    if(mysqli_num_rows($query)==0){
      return null;
    }else{
      return mysqli_fetch_array($query);
    }
  }

}
