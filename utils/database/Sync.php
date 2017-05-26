<?php
class Sync{

  public static function update_after_offset($offset,$local_db,$shared_db,$my_fed,$limit=100){
    $str="select * from lo_update_log where id > $offset limit $limit";
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
        $statement = $local_db->prepare("delete from lo_update_log where id <= ? and  local_id = ?");
        $statement->bind_param("ii",$row["id"],$row["local_id"]);
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
        $statement->bind_param("si",$my_fed,$row["Id_Lo"]);
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
            Structure,Aggregation_Level,TimeUpd
          ) "
            ."values (?,?,?,?,?,?,?,?,?,?)";
        $statement = $shared_db->prepare($str);
        $statement->bind_param("sissssssii",
          $row["Id_Fd"],$row["Id_Lo"],
          $row["Title"],$row["Language"],
          $row["Description"],$row["Keyword"],$row["Coverage"],
          $row["Structure"],$row["Aggregation_Level"],$row["TimeUpd"]
        );
        /*
          there's actually no need to insert it right away,
          the other daemons can download the newest version themselves
          during the next sync routine
        */
        $statement->execute();
        $statement->close();

        /*updating side lifecycle of the object to shared database*/
        $string = "select * from lo_lifecycle where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
        $result_tmp = $local_db->query($string);
        if(mysqli_num_rows($result_tmp) >= 1){
          $item = mysqli_fetch_array($result_tmp);
          /*uploading lo_lifecycle of the object to shared database*/
          $statement = $shared_db->prepare("insert into lo_lifecycle(Id_Lo,Id_Fd,Version,Status) value(?,?,?,?)");
          $statement->bind_param("isss",$item["Id_Lo"],$item["Id_Fd"],$item["Version"],$item["Status"]);
          $statement->execute();
          $statement->close();
        }


        /*updating meta-metadata of the object to shared database*/
        $string = "select * from lo_metadata where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
        $result_tmp = $local_db->query($string);
        if(mysqli_num_rows($result_tmp) >= 1){
          $item = mysqli_fetch_array($result_tmp);
          /*updating lo_lifecycle of the object to shared database*/
          $statement = $shared_db->prepare("insert into lo_metadata(Id_Lo,Id_Fd,MetadataSchema,Language) value(?,?,?,?)");
          $statement->bind_param("isss",$item["Id_Lo"],$item["Id_Fd"],$item["MetadataSchema"],$item["Language"]);
          $statement->execute();
          $statement->close();
        }


        /*updating technical of the object to shared database*/
        $string = "select * from lo_technical where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
        $result_tmp = $local_db->query($string);
        if(mysqli_num_rows($result_tmp) >= 1){
          $item = mysqli_fetch_array($result_tmp);
          /*uploading lo_lifecycle of the object to shared database*/
          $statement = $shared_db->prepare("insert into lo_technical(Id_Lo,Id_Fd,Format,Size,Location,InstallationRemarks,OtherPlatformRequirements,Duration) value(?,?,?,?,?,?,?,?)");
          $statement->bind_param("isssssss",$item["Id_Lo"],$item["Id_Fd"],$item["Format"],$item["size"],$item["Location"],$item["InstallationRemarks"],$item["OtherPlatformRequirements"],$item["Duration"]);
          $statement->execute();
          $statement->close();
        }

        /*updating educational of the object to shared database*/
        $string = "select * from lo_educational where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
        $result_tmp = $local_db->query($string);
        if(mysqli_num_rows($result_tmp) >= 1){
          $item = mysqli_fetch_array($result_tmp);
          /*uploading lo_lifecycle of the object to shared database*/
          $statement = $shared_db->prepare("insert into lo_educational(Id_Lo,Id_Fd,InteractivityType,LearningResourceType,InteractivityLevel,SemanticDensity,IntendedEndUserRole,Context,TypicalAgeRange,Difficulty,TypicalLearningTime,edu_Description,edu_Language) value(?,?,?,?,?,?,?,?,?,?,?,?,?)");
          $statement->bind_param("issssssssssss",
          $item["Id_Lo"],$item["Id_Fd"],$item["InteractivityType"],$item["LearningResourceType"],
          $item["InteractivityLevel"],$item["SemanticDensity"],$item["IntendedEndUserRole"],
          $item["Context"],$item["TypicalAgeRange"],$item["Difficulty"],
          $item["TypicalLearningTime"],$item["edu_Description"],$item["edu_Language"]);
          $statement->execute();
          $statement->close();
        }

        /*updating rights of the object to shared database*/
        $string = "select * from lo_rights where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
        $result_tmp = $local_db->query($string);
        if(mysqli_num_rows($result_tmp) >= 1){
          $item = mysqli_fetch_array($result_tmp);
          /*uploading lo_lifecycle of the object to shared database*/
          $statement = $shared_db->prepare("insert into lo_rights(Id_Lo,Id_Fd,Cost,Copyright,rights_Description) value(?,?,?,?,?)");
          $statement->bind_param("issss",$item["Id_Lo"],$item["Id_Fd"],$item["Cost"],$item["Copyright"],$item["rights_Description"]);
          $statement->execute();
          $statement->close();
        }

        /*updating relation of the object to the shared database*/
        $string = "select * from lo_relation where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
        $result_tmp = $local_db->query($string);
        if(mysqli_num_rows($result_tmp) >= 1){
          while($item = mysqli_fetch_array($result_tmp)){
            /*uploading lo_lifecycle of the object to shared database*/
            $statement = $shared_db->prepare("insert into lo_relation(Id_Lo,Id_Fd,Id_Target,Kind,TimeUpd) value(?,?,?,?,?)");
            $statement->bind_param("isisi",$item["Id_Lo"],$item["Id_Fd"],$item["Id_Target"],$item["Kind"],$item["TimeUpd"]);
            $statement->execute();
            $statement->close();
          }
        }

        /*updating file of the object to the shared database*/
        $string = "select * from lo_file where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
        $result_tmp = $local_db->query($string);
        if(mysqli_num_rows($result_tmp) >= 1){
          while($item = mysqli_fetch_array($result_tmp)){
            /*uploading lo_lifecycle of the object to shared database*/
            $statement = $shared_db->prepare("insert into lo_file(Id_Lo,Id_Fd,url,filename,filesize,filemime) value(?,?,?,?,?,?)");
            $statement->bind_param("isssss",$item["Id_Lo"],$item["Id_Fd"],$item["url"],$item["filename"],$item["filesize"],$item["filemime"]);
            $statement->execute();
            $statement->close();
          }
        }

        /*updating contribute of the object to the shared database*/
        $string = "select * from lo_contribute where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
        $result_tmp = $local_db->query($string);
        if(mysqli_num_rows($result_tmp) >= 1){
          while($item = mysqli_fetch_array($result_tmp)){
            $statement = $shared_db->prepare("insert into lo_contribute(Id_Lo,Id_Fd,Role,Entity,Date) value(?,?,?,?,?)");
            $statement->bind_param("issss",$item["Id_Lo"],$item["Id_Fd"],$item["Role"],$item["Entity"],$item["Date"]);
            $statement->execute();
            $statement->close();
          }
        }else{
            echo "\n\n\tNOT OK CONTRIBUTE\n\n";
        }

        echo "\n\t\t>>Row ".$row["id"]." has been updated.";
      }
    }
  }

  public static function update_all($local_db,$shared_db,$my_fed,$limit=100){
    Sync::update_after_offset(0,$local_db,$shared_db,$my_fed,$limit);
  }


  //uploads data from left database (starting from row $offset) to right database
  public static function upload_after_offset($offset,$local_db,$shared_db,$my_fed,$limit){

      $str="select * from lo_general as G inner join lo_lifecycle as L using(Id_Lo,Id_Fd) where G.Id_Lo > $offset and Id_Fd like '$my_fed' limit $limit";
      $result=$local_db->query($str);
      $drafts_counter=0;
      while($row=mysqli_fetch_array($result)){

        if($row["Status"]!='draft'){
          $str="insert into lo_general(
              Id_Fd,Id_Lo,
              Title,Language,
              Description,Keyword,Coverage,
              Structure,Aggregation_Level,TimeUpd
            ) "
              ."values (?,?,?,?,?,?,?,?,?,?)";
          $statement = $shared_db->prepare($str);
          $statement->bind_param("sissssssii",
            $row["Id_Fd"],$row["Id_Lo"],
            $row["Title"],$row["Language"],
            $row["Description"],$row["Keyword"],$row["Coverage"],
            $row["Structure"],$row["Aggregation_Level"],$row["TimeUpd"]
          );
          $statement->execute();
          $tmp_insert_id=$statement->insert_id;
          $statement->close();


          /*uploading lifecycle of the object to shared database*/
          $string = "select * from lo_lifecycle where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
          $result_tmp = $local_db->query($string);
          if(mysqli_num_rows($result_tmp) >= 1){
            $item = mysqli_fetch_array($result_tmp);
            $statement = $shared_db->prepare("insert into lo_lifecycle(Id_Lo,Id_Fd,Version,Status) value(?,?,?,?)");
            $statement->bind_param("isss",$item["Id_Lo"],$item["Id_Fd"],$item["Version"],$item["Status"]);
            $statement->execute();
            $statement->close();
          }


          /*uploading meta-metadata of the object to shared database*/
          $string = "select * from lo_metadata where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
          $result_tmp = $local_db->query($string);
          if(mysqli_num_rows($result_tmp) >= 1){
            $item = mysqli_fetch_array($result_tmp);
            $statement = $shared_db->prepare("insert into lo_metadata(Id_Lo,Id_Fd,MetadataSchema,Language) value(?,?,?,?)");
            $statement->bind_param("isss",$item["Id_Lo"],$item["Id_Fd"],$item["MetadataSchema"],$item["Language"]);
            $statement->execute();
            $statement->close();
          }


          /*uploading technical of the object to shared database*/
          $string = "select * from lo_technical where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
          $result_tmp = $local_db->query($string);
          if(mysqli_num_rows($result_tmp) >= 1){
            $item = mysqli_fetch_array($result_tmp);
            $statement = $shared_db->prepare("insert into lo_technical(Id_Lo,Id_Fd,Format,Size,Location,InstallationRemarks,OtherPlatformRequirements,Duration) value(?,?,?,?,?,?,?,?)");
            $statement->bind_param("isssssss",$item["Id_Lo"],$item["Id_Fd"],$item["Format"],$item["size"],$item["Location"],$item["InstallationRemarks"],$item["OtherPlatformRequirements"],$item["Duration"]);
            $statement->execute();
            $statement->close();
          }

          /*uploading educational of the object to shared database*/
          $string = "select * from lo_educational where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
          $result_tmp = $local_db->query($string);
          if(mysqli_num_rows($result_tmp) >= 1){
            $item = mysqli_fetch_array($result_tmp);
            $statement = $shared_db->prepare("insert into lo_educational(Id_Lo,Id_Fd,InteractivityType,LearningResourceType,InteractivityLevel,SemanticDensity,IntendedEndUserRole,Context,TypicalAgeRange,Difficulty,TypicalLearningTime,edu_Description,edu_Language) value(?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $statement->bind_param("issssssssssss",
            $item["Id_Lo"],$item["Id_Fd"],$item["InteractivityType"],$item["LearningResourceType"],
            $item["InteractivityLevel"],$item["SemanticDensity"],$item["IntendedEndUserRole"],
            $item["Context"],$item["TypicalAgeRange"],$item["Difficulty"],
            $item["TypicalLearningTime"],$item["edu_Description"],$item["edu_Language"]);
            $statement->execute();
            $statement->close();
          }

          /*uploading rights of the object to shared database*/
          $string = "select * from lo_rights where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
          $result_tmp = $local_db->query($string);
          if(mysqli_num_rows($result_tmp) >= 1){
            $item = mysqli_fetch_array($result_tmp);
            $statement = $shared_db->prepare("insert into lo_rights(Id_Lo,Id_Fd,Cost,Copyright,rights_Description) value(?,?,?,?,?)");
            $statement->bind_param("issss",$item["Id_Lo"],$item["Id_Fd"],$item["Cost"],$item["Copyright"],$item["rights_Description"]);
            $statement->execute();
            $statement->close();
          }

          /*uploading relation of the object to the shared database*/
          $string = "select * from lo_relation where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
          $result_tmp = $local_db->query($string);
          if(mysqli_num_rows($result_tmp) >= 1){
            while($item = mysqli_fetch_array($result_tmp)){
              $statement = $shared_db->prepare("insert into lo_relation(Id_Lo,Id_Fd,Id_Target,Kind,TimeUpd) value(?,?,?,?,?)");
              $statement->bind_param("isisi",$item["Id_Lo"],$item["Id_Fd"],$item["Id_Target"],$item["Kind"],$item["TimeUpd"]);
              $statement->execute();
              $statement->close();
            }
          }

          /*uploading file of the object to the shared database*/
          $string = "select * from lo_file where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
          $result_tmp = $local_db->query($string);
          if(mysqli_num_rows($result_tmp) >= 1){
            while($item = mysqli_fetch_array($result_tmp)){
              $statement = $shared_db->prepare("insert into lo_file(Id_Lo,Id_Fd,url,filename,filesize,filemime) value(?,?,?,?,?,?)");
              $statement->bind_param("isssss",$item["Id_Lo"],$item["Id_Fd"],$item["url"],$item["filename"],$item["filesize"],$item["filemime"]);
              $statement->execute();
              $statement->close();
            }
          }

          /*uploading contribute of the object to the shared database*/
          $string = "select * from lo_contribute where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
          $result_tmp = $local_db->query($string);
          if(mysqli_num_rows($result_tmp) >= 1){
            while($item = mysqli_fetch_array($result_tmp)){
              $statement = $shared_db->prepare("insert into lo_contribute(Id_Lo,Id_Fd,Role,Entity,Date) value(?,?,?,?,?)");
              $statement->bind_param("issss",$item["Id_Lo"],$item["Id_Fd"],$item["Role"],$item["Entity"],$item["Date"]);
              $statement->execute();
              $statement->close();
            }
          }else{
              echo "\n\n\tNOT OK CONTRIBUTE\n\n";
          }


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
  public static function upload_all($local_db,$shared_db,$my_fed,$limit=100){
    Sync::upload_after_offset(0,$local_db,$shared_db,$my_fed,$limit);
  }


  public static function download_after_offset($offset,$shared_db,$local_db,$my_fed,$limit=100){
    $string = "select * from lo_general where Id_Fd != '$my_fed' AND id > $offset limit $limit";
    $query=$shared_db->query($string);
    while($row=mysqli_fetch_array($query)){
      /*
        Deleting duplicate entries and writing a new one using the
        same IDs but with updated data (simulates an update query)
      */
      $statement=$local_db->prepare("delete from lo_general where Id_Fd like ? and Id_Lo = ?");
      $statement->bind_param("si",$row["Id_Fd"],$row["Id_Lo"]);
      if($statement->execute() == false) echo "\n\t\tERROR:".$statement->error;
      $deleted_rows = $statement->affected_rows;
      $statement->close();
      $string = 'select status from lo_lifecycle where Id_Lo = '.$row["Id_Lo"].' and Id_Fd like \''.$row["Id_Fd"].'\'';
      $query2=$shared_db->query($string);
      $item=mysqli_fetch_array($query2);

      if($deleted_rows == 0){
        /*creating node*/
        $type="linkableobject";
        $tmp_user=0;
        $tmp_status=1;
        $tmp_created=time();
        $tmp_comment = 2;
        $tmp_promote = 1;
        $tmp_sticky = 0;
        $tmp_tnid = 0;
        $tmp_translate = 0;

        /*node statement*/
        $statement = $local_db->prepare("insert into node("
        ."nid,"
        ."vid,Id_Fd,type,"
        ."language,title,uid,"
        ."status,created,changed,"
        ."comment,promote,sticky,"
        ."tnid,translate) value(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

        $statement->bind_param("iissssiiiiiiiii",
          $row["Id_Lo"],
          $row["Id_Lo"] ,$row["Id_Fd"], $type,
          $row["Language"], $row["Title"], $tmp_user,
          $tmp_status, $tmp_created, $tmp_created,
          $tmp_comment, $tmp_promote, $tmp_sticky,
          $tmp_tnid, $tmp_translate
        );
        $statement->execute();
        $tmp_nid = $statement->insert_id;
        $statement->close();

        $tmp_gid = 0;
        $tmp_realm = "all";
        $tmp_grant_view = 1;
        $tmp_grant_update = 0;
        $tmp_grant_delete = 0;

        /*node_access statement*/
        $statement = $local_db->prepare("insert into node_access(nid,gid,realm,grant_view,grant_update,grant_delete) value(?,?,?,?,?,?)");
        $statement->bind_param("iisiii",$tmp_nid,$tmp_gid,$tmp_realm,$tmp_grant_view,$tmp_grant_update,$tmp_grant_delete);
        $statement->execute();
        $statement->close();

        $tmp_log = "";

        /*node_revision statement*/
        $statement = $local_db->prepare("insert into node_revision("
        ."nid,vid,uid,"
        ."title,log,timestamp,"
        ."status,comment,promote,"
        ."sticky) value(?,?,?,?,?,?,?,?,?,?)");
        $statement->bind_param("iiissiiiii",
          $tmp_nid,$row["Id_Lo"],$tmp_user,
          $row["Title"],$tmp_log,$tmp_created,
          $tmp_status,$tmp_comment,$tmp_promote,
          $tmp_sticky
        );
        $statement->execute();
        $statement->close();

        $tmp_cid = 0;
        $tmp_comment_count = 0;
        $statement = $local_db->prepare("insert into node_comment_statistics(nid,cid,last_comment_timestamp,last_comment_uid,comment_count) value(?,?,?,?,?)");
        $statement->bind_param("iiiii",$tmp_nid,$tmp_cid,$tmp_created,$tmp_user,$tmp_comment_count);
        $statement->execute();
        $statement->close();
      }else{
        /*updating existing node*/

        $string = "select nid from node where vid = ".$row["Id_Lo"]." and Id_Fd like '".$row["Id_Fd"]."'";
        $query3=$local_db->query($string);
        $tmp_nid=mysqli_fetch_array($query3);

        $tmp_changed=time();
        $statement = $local_db->prepare("update node set language = ?, title = ?, changed = ? where nid = ?");
        $statement->bind_param("ssii",$row["Language"],$row["Title"],$tmp_changed,$tmp_nid);
        $statement->execute();
        $statement->close();

        $statement = $local_db->prepare("update node_revision set title = ?, timestamp = ? where nid = ?");
        $statement->bind_param("sii",$row["Title"],$tmp_changed,$tmp_nid);
        $statement->execute();
        $statement->close();
      }


      $str="insert into lo_general(
          Id_Lo, Id_Fd, shared_id,
          Title, Language, Description,
          Keyword, Coverage, Structure,
          Aggregation_Level, TimeUpd
        ) "
          ."value (?,?,?,?,?,?,?,?,?,?,?)";
      $statement = $local_db->prepare($str);
      $statement->bind_param("isissssssii",
        $row["Id_Lo"], $row["Id_Fd"], $row["id"],
        $row["Title"], $row["Language"], $row["Description"],
        $row["Keyword"], $row["Coverage"], $row["Structure"],
        $row["Aggregation_Level"], $row["TimeUpd"]
      );
      if($statement->execute() == false) echo "\n\t\tERROR: ".$statement->error;
      $tmp_insert_id = $statement->insert_id;
      $statement->close();


      /*downloading lifecycle of the object*/
      $string = "select * from lo_lifecycle where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
      $result_tmp = $shared_db->query($string);
      if(mysqli_num_rows($result_tmp) >= 1){
        $item = mysqli_fetch_array($result_tmp);
        $statement = $local_db->prepare("insert into lo_lifecycle(Id_Lo,Id_Fd,Version,Status) value(?,?,?,?)");
        $statement->bind_param("isss",$item["Id_Lo"],$item["Id_Fd"],$item["Version"],$item["Status"]);
        $statement->execute();
        $statement->close();
      }


      /*downloading meta-metadata of the object*/
      $string = "select * from lo_metadata where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
      $result_tmp = $local_db->query($string);
      if(mysqli_num_rows($result_tmp) >= 1){
        $item = mysqli_fetch_array($result_tmp);
        $statement = $shared_db->prepare("insert into lo_metadata(Id_Lo,Id_Fd,MetadataSchema,Language) value(?,?,?,?)");
        $statement->bind_param("isss",$item["Id_Lo"],$item["Id_Fd"],$item["MetadataSchema"],$item["Language"]);
        $statement->execute();
        $statement->close();
      }


      /*downloading technical of the object*/
      $string = "select * from lo_technical where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
      $result_tmp = $shared_db->query($string);
      if(mysqli_num_rows($result_tmp) >= 1){
        $item = mysqli_fetch_array($result_tmp);
        $statement = $local_db->prepare("insert into lo_technical(Id_Lo,Id_Fd,Format,Size,Location,InstallationRemarks,OtherPlatformRequirements,Duration) value(?,?,?,?,?,?,?,?)");
        $statement->bind_param("isssssss",$item["Id_Lo"],$item["Id_Fd"],$item["Format"],$item["size"],$item["Location"],$item["InstallationRemarks"],$item["OtherPlatformRequirements"],$item["Duration"]);
        $statement->execute();
        $statement->close();
      }

      /*downloading educational of the object*/
      $string = "select * from lo_educational where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
      $result_tmp = $shared_db->query($string);
      if(mysqli_num_rows($result_tmp) >= 1){
        $item = mysqli_fetch_array($result_tmp);
        $statement = $local_db->prepare("insert into lo_educational(Id_Lo,Id_Fd,InteractivityType,LearningResourceType,InteractivityLevel,SemanticDensity,IntendedEndUserRole,Context,TypicalAgeRange,Difficulty,TypicalLearningTime,edu_Description,edu_Language) value(?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $statement->bind_param("issssssssssss",
        $item["Id_Lo"],$item["Id_Fd"],$item["InteractivityType"],$item["LearningResourceType"],
        $item["InteractivityLevel"],$item["SemanticDensity"],$item["IntendedEndUserRole"],
        $item["Context"],$item["TypicalAgeRange"],$item["Difficulty"],
        $item["TypicalLearningTime"],$item["edu_Description"],$item["edu_Language"]);
        $statement->execute();
        $statement->close();
      }

      /*downloading rights of the object*/
      $string = "select * from lo_rights where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
      $result_tmp = $shared_db->query($string);
      if(mysqli_num_rows($result_tmp) >= 1){
        $item = mysqli_fetch_array($result_tmp);
        $statement = $local_db->prepare("insert into lo_rights(Id_Lo,Id_Fd,Cost,Copyright,rights_Description) value(?,?,?,?,?)");
        $statement->bind_param("issss",$item["Id_Lo"],$item["Id_Fd"],$item["Cost"],$item["Copyright"],$item["rights_Description"]);
        $statement->execute();
        $statement->close();
      }

      /*downloading relation of the object*/
      $string = "select * from lo_relation where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
      $result_tmp = $shared_db->query($string);
      if(mysqli_num_rows($result_tmp) >= 1){
        while($item = mysqli_fetch_array($result_tmp)){
          $statement = $local_db->prepare("insert into lo_relation(Id_Lo,Id_Fd,Id_Target,Kind,TimeUpd) value(?,?,?,?,?)");
          $statement->bind_param("isisi",$item["Id_Lo"],$item["Id_Fd"],$item["Id_Target"],$item["Kind"],$item["TimeUpd"]);
          $statement->execute();
          $statement->close();
        }
      }

      /*downloading file of the object*/
      $string = "select * from lo_file where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
      $result_tmp = $shared_db->query($string);
      if(mysqli_num_rows($result_tmp) >= 1){
        while($item = mysqli_fetch_array($result_tmp)){
          $statement = $local_db->prepare("insert into lo_file(Id_Lo,Id_Fd,url,filename,filesize,filemime) value(?,?,?,?,?,?)");
          $statement->bind_param("isssss",$item["Id_Lo"],$item["Id_Fd"],$item["url"],$item["filename"],$item["filesize"],$item["filemime"]);
          $statement->execute();
          $statement->close();
        }
      }

      /*downloading contribute of the object*/
      $string = "select * from lo_contribute where Id_Fd like '".$row["Id_Fd"]."' and Id_Lo = ".$row["Id_Lo"];
      $result_tmp = $shared_db->query($string);
      if(mysqli_num_rows($result_tmp) >= 1){
        while($item = mysqli_fetch_array($result_tmp)){
          $statement = $local_db->prepare("insert into lo_contribute(Id_Lo,Id_Fd,Role,Entity,Date) value(?,?,?,?,?)");
          $statement->bind_param("issss",$item["Id_Lo"],$item["Id_Fd"],$item["Role"],$item["Entity"],$item["Date"]);
          $statement->execute();
          $statement->close();
        }
      }


      echo "\n\t\t<<Row ".$row["id"]." has been downloaded.";
    }
  }

  public static function download_all($shared_db,$local_db,$my_fed,$limit=100){
    Sync::download_after_offset(0,$shared_db,$local_db,$my_fed,$limit=100);
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
