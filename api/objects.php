<?php

{ // save Objects. will validate and then save to the db, giving the status object in return.
    function savePlayerObject(&$db, $player)
    {
        $r = array('class'=>null,'code'=>null,'details'=>null,'debug'=>null);
        { // validate the object
            if ((isset($player) && is_array($player))
                && (isset($player['guid']) && is_string($player['guid']))
                && ((isset($player['name']) && is_string($player['name']))
                 || (isset($player['plain']) && is_string($player['plain'])))
                && (isset($player['team']) && (is_int($player['team']) || is_string($player['team'])))
                && (isset($player['region']) && is_int($player['region']))
                ) 
            {
                // pass, it's a valid object!
                // now make sure its the style we need:
                if (is_string($player['team']))
                { 
                    switch(strtolower($player['team'])) 
                    { // convert the ALIENS/RESISTANCE scheme to #
                        case 'resistance':
                            $player['team'] = 1;
                            break;
                        case 'aliens':
                            $player['team'] = 2;
                            break;
                        case 'enlightened':
                            $player['team'] = 2;
                            break;
                        default :
                            $player['team'] = 3;
                            break;
                    }
                }
                
                if (!isset($player['name'])) $player['name'] = $player['plain'];
            }
            else
            {
                $r['class'] = 'fail_object';
                $r['code'] = 500;
                $r['detail'] = 'The provided PlayerObject does not appear valid.';
                $r['debug'] = $player;
                return $r;
            }
        }
        
        { // build the query
            $parms = array();
            $query = "INSERT INTO `ingress`.`players` (`guid`, `name`, `team`, `region`) VALUES (:guid, :name, :team, :region)
ON DUPLICATE KEY UPDATE `name`=:name, `team`=:team, `region`=:region;";
            $parms[] = array(':guid',$player['guid']);
            $parms[] = array(':name',$player['name']);
            $parms[] = array(':team',$player['team']);
            $parms[] = array(':region',$player['region']);
                
            $stmt = $db->prepare($query);
            foreach($parms as $parm) {
                $stmt->bindValue($parm[0], $parm[1]);    
            }
        }

        { // execute the insert/update
            try 
            {
                $stmt->execute();
                ob_start();
                    print_r($parms);
                    print_r($stmt);
                $r['debug'] = ob_get_clean();
                
                if ($stmt->rowCount() > 0) 
                {
                    $r['class'] = 'success';
                    $r['code'] = 201;
                    $r['detail'] = 'player updated';
                } 
                else
                {
                    $r['class'] = 'warn_insert';
                    $r['code'] = 206;
                    $r['detail'] = 'no change to player info has occured';
                }
            }
            catch (PDOException $e)
            {
                $r['class'] = 'fail_query';
                $r['code'] = 500;
                $r['detail'] = $e->getMessage();
            }
        }
        
        return $r;
    }
    
    function savePortalObject(&$db, $portal)
    {
        $r = array('class'=>null,'code'=>null,'details'=>null,'debug'=>null);
        { // validate the object
            if ((isset($portal) && is_array($portal))
                && (isset($portal['guid']) && is_string($portal['guid']))
                && (isset($portal['address']) && is_string($portal['address']))
                && (isset($portal['latE6']) && is_int($portal['latE6']))
                && (isset($portal['lngE6']) && is_int($portal['lngE6']))
                && (isset($portal['name']) && is_string($portal['name']))
                && (isset($portal['team']) && (is_int($portal['team']) || is_string($portal['team'])))
                && (isset($portal['region']) && is_int($portal['region']))
                ) 
            {
                // pass, it's a valid object!
                // now make sure its the style we need:
                if (is_string($portal['team']))
                { 
                    switch(strtolower($portal['team'])) 
                    { // convert the ALIENS/RESISTANCE scheme to #
                        case 'resistance':
                            $portal['team'] = 1;
                            break;
                        case 'aliens':
                            $portal['team'] = 2;
                            break;
                        case 'enlightened':
                            $portal['team'] = 2;
                            break;
                        default :
                            $portal['team'] = 3;
                            break;
                    }
                }
            }
            else
            {
                $r['class'] = 'fail_object';
                $r['code'] = 500;
                $r['detail'] = 'The provided PortalObject does not appear valid.';
                $r['debug'] = $portal;
                return $r;
            }
        }
        
        { // build the query
            $parms = array();
            $query = "INSERT INTO `ingress`.`portals` (`guid`, `address`, `latE6`, `lngE6`, `name`, `team`, `region`) VALUES (:guid, :address, :latE6, :lngE6, :name, :team, :region)
ON DUPLICATE KEY UPDATE `team`=:team, `region`=:region;";
            $parms[] = array(':guid',$portal['guid']);
            $parms[] = array(':address',$portal['address']);
            $parms[] = array(':latE6',$portal['latE6']);
            $parms[] = array(':lngE6',$portal['lngE6']);
            $parms[] = array(':name',$portal['name']);
            $parms[] = array(':team',$portal['team']);
            $parms[] = array(':region',$portal['region']);
                
            $stmt = $db->prepare($query);
            foreach($parms as $parm) {
                $stmt->bindValue($parm[0], $parm[1]);    
            }
        }

        { // execute the insert/update
            try 
            {
                $stmt->execute();
                ob_start();
                    print_r($parms);
                    print_r($stmt);
                $r['debug'] = ob_get_clean();
                
                if ($stmt->rowCount() > 0) 
                {
                    $r['class'] = 'success';
                    $r['code'] = 201;
                    $r['detail'] = 'portal updated';
                } 
                else
                {
                    $r['class'] = 'warn_insert';
                    $r['code'] = 206;
                    $r['detail'] = 'no change to portal info required';
                }
            }
            catch (PDOException $e)
            {
                $r['class'] = 'fail_query';
                $r['code'] = 500;
                $r['detail'] = $e->getMessage();
            }
        }
        
        return $r;
    }
    
    function saveChatObject(&$db, $chat)
    {
        $r = array('class'=>null,'code'=>null,'details'=>null,'debug'=>null);
        { // validate the object
            if ((isset($chat) && is_array($chat))
                && (isset($chat['guid']) && is_string($chat['guid']))
                && (isset($chat['datetime']) && is_int($chat['datetime']))
                && (isset($chat['user']) && is_string($chat['user']))
                && (isset($chat['text']) && is_string($chat['text']))
                && (isset($chat['secure']) && (is_bool($chat['secure']) || is_int($chat['secure'])))
                && (isset($chat['region']) && is_int($chat['region']))
                ) 
            {
                // pass, it's a valid object!
                // now make sure its the style we need:
                if ($chat['secure'] === true) $chat['secure'] = 1;
                else $chat['secure'] = 0;
                if ($chat['datetime'] > 4294967295) // maximum valid datetime in s, so it must be ms
                    $chat['datetime'] = $chat['datetime'] / 1000; //convert it!
            }
            else
            {
                $r['class'] = 'fail_object';
                $r['code'] = 500;
                $r['detail'] = 'The provided ChatObject does not appear valid.';
                $r['debug'] = $chat;
            }
        }
        
        { // build the query
            $parms = array();
            $query = "INSERT INTO `ingress`.`chat_log` (`guid`, `datetime`, `user`, `text`, `secure`, `region`) VALUES (:guid, :datetime, :user, :text, :secure, :region) 
            ON DUPLICATE KEY UPDATE `secure`=:secure, `datetime`=:datetime, `region`=:region";
            $parms[] = array(':guid',$chat['guid']);
            $parms[] = array(':datetime',$chat['datetime']);
            $parms[] = array(':user',$chat['user']);
            $parms[] = array(':text',$chat['text']);
            $parms[] = array(':secure',$chat['secure']);
            $parms[] = array(':region',$chat['region']);
                
            $stmt = $db->prepare($query);
            foreach($parms as $parm) {
                $stmt->bindValue($parm[0], $parm[1]);    
            }
        }

        { // execute the insert/update
            try 
            {
                $stmt->execute();
                ob_start();
                    echo "chat :";
                    print_r($chat);
                    echo "parms :";
                    print_r($parms);
                    echo "stmt :";
                    print_r($stmt);
                $r['debug'] = ob_get_clean();
                
                if ($stmt->rowCount() > 0) 
                {
                    $r['class'] = 'success';
                    $r['code'] = 201;
                    $r['detail'] = 'chat log updated';
                } 
                else
                {
                    $r['class'] = 'warn_insert';
                    $r['code'] = 206;
                    $r['detail'] = 'chat log already exists.';
                }
            }
            catch (PDOException $e)
            {
                $r['class'] = 'fail_query';
                $r['code'] = 500;
                $r['detail'] = $e->getMessage();
            }
        }
        
        return $r;
    }
    
    function saveCaptureObject(&$db, $capture)
    {
        $r = array('class'=>null,'code'=>null,'details'=>null,'debug'=>null);
        { // validate the object
            if ((isset($capture) && is_array($capture))
                && (isset($capture['guid']) && is_string($capture['guid']))
                && (isset($capture['user']) && is_string($capture['user']))
                && (isset($capture['portal']) && is_string($capture['portal']))
                && (isset($capture['datetime']) && is_int($capture['datetime']))
                && (isset($capture['region']) && is_int($capture['region']))
                ) 
            {
                // pass, it's a valid object!
                // now make sure its the style we need:
                if ($capture['datetime'] >= 4294967295) // maximum valid datetime in s, so it must be ms... unless it really is the year 2136 already!?
                    $capture['datetime'] = $capture['datetime'] / 1000; //convert it!
            }
            else
            {
                $r['class'] = 'fail_object';
                $r['code'] = 500;
                $r['detail'] = 'The provided CaptureObject does not appear valid.';
                $r['debug'] = $capture;
                return $r;
            }
        }
        
        { // build the query
            $parms = array();
            $query = sprintf("INSERT INTO `ingress`.`capture_log` (`guid`, `datetime`, `user`, `portal`, `region`) VALUES (:guid, :datetime, :user, :portal, :region) 
            ON DUPLICATE KEY UPDATE `datetime`=:datetime, `region`=:region;");
            $parms[] = array(':guid',$capture['guid']);
            $parms[] = array(':datetime',$capture['datetime']);
            $parms[] = array(':user',$capture['user']);
            $parms[] = array(':portal',$capture['portal']);
            $parms[] = array(':region',$capture['region']);
                
            $stmt = $db->prepare($query);
            foreach($parms as $parm) {
                $stmt->bindValue($parm[0], $parm[1]);    
            }
        }

        { // execute the insert/update
            try 
            {
                $stmt->execute();
                ob_start();
                    echo "capture :";
                    print_r($capture);
                    echo "parms :";
                    print_r($parms);
                    echo "stmt :";
                    print_r($stmt);
                $r['debug'] = ob_get_clean();
                
                if ($stmt->rowCount() > 0) 
                {
                    $r['class'] = 'success';
                    $r['code'] = 201;
                    $r['detail'] = "capture log updated";
                } 
                else
                {
                    $r['class'] = 'warn_insert';
                    $r['code'] = 206;
                    $r['detail'] = "capture log already exists.";
                }
            }
            catch (PDOException $e)
            {
                $r['class'] = 'fail_query';
                $r['code'] = 500;
                $r['detail'] = $e->getMessage();
            }
        }
        
        return $r;
    }

    function saveResonatorObject(&$db, $action, $resonator)
    {
        $r = array('class'=>null,'code'=>null,'details'=>null,'debug'=>null);
        { // validate the object
            if ((isset($resonator) && is_array($resonator))
                && (isset($resonator['guid']) && is_string($resonator['guid']))
                && (isset($resonator['user']) && is_string($resonator['user']))
                && (isset($resonator['portal']) && is_string($resonator['portal']))
                && (isset($resonator['res']) && is_string($resonator['res']))
                && (isset($resonator['datetime']) && is_int($resonator['datetime']))
                && (isset($resonator['region']) && is_int($resonator['region']))
                && (isset($action))
                ) 
            {
                // pass, it's a valid object!
                // now make sure its the style we need:
                if ($resonator['datetime'] >= 4294967295) // maximum valid datetime in s, so it must be ms... unless it really is the year 2136 already!?
                    $resonator['datetime'] = $resonator['datetime'] / 1000; //convert it!
                
                if ($action != 'deploy' 
                    && $action != 'destroy') 
                    $action = 'deploy';
            }
            else
            {
                $r['class'] = 'fail_object';
                $r['code'] = 500;
                $r['detail'] = 'The provided ResonatorObject does not appear valid.';
                $r['debug'] = $resonator;
                return $r;
            }
        }
        
        { // build the query
            $parms = array();
            $query = sprintf("INSERT INTO `ingress`.`%s_log` (`guid`, `datetime`, `user`, `portal`, `res`, `region`) VALUES (:guid, :datetime, :user, :portal, :res, :region)
            ON DUPLICATE KEY UPDATE `datetime`=:datetime, `res`=:res, `region`=:region;",
            $action);
            $parms[] = array(':guid',$resonator['guid']);
            $parms[] = array(':datetime',$resonator['datetime']);
            $parms[] = array(':user',$resonator['user']);
            $parms[] = array(':portal',$resonator['portal']);
            $parms[] = array(':res',$resonator['res']);
            $parms[] = array(':region',$resonator['region']);
                
            $stmt = $db->prepare($query);
            foreach($parms as $parm) {
                $stmt->bindValue($parm[0], $parm[1]);    
            }
        }

        { // execute the insert/update
            try 
            {
                $stmt->execute();
                ob_start();
                    echo "resonator  :";
                    print_r($resonator);
                    echo "parms :";
                    print_r($parms);
                    echo "stmt :";
                    print_r($stmt);
                $r['debug'] = ob_get_clean();
                
                if ($stmt->rowCount() > 0) 
                {
                    $r['class'] = 'success';
                    $r['code'] = 201;
                    $r['detail'] = "resonator:{$action}-log updated";
                } 
                else
                {
                    $r['class'] = 'warn_insert';
                    $r['code'] = 206;
                    $r['detail'] = "resonator:{$action}-log already exists.";
                }
            }
            catch (PDOException $e)
            {
                $r['class'] = 'fail_query';
                $r['code'] = 500;
                $r['detail'] = $e->getMessage();
            }
        }
        
        return $r;
    }
    
    function saveLinkObject(&$db, $action, $link)
    {
        $r = array('class'=>null,'code'=>null,'details'=>null,'debug'=>null);
        { // validate the object
            if ((isset($link) && is_array($link))
                && (isset($link['guid']) && is_string($link['guid']))
                && (isset($link['portal1']) && is_string($link['portal1']))
                && (isset($link['portal2']) && is_string($link['portal2']))
                && (isset($link['datetime']) && is_int($link['datetime']))
                && (isset($link['region']) && is_int($link['region']))
                && (isset($action))
                ) 
            {
                // pass, it's a valid object!
                // now make sure its the style we need:
                if ($link['datetime'] >= 4294967295) // maximum valid datetime in s, so it must be ms... unless it really is the year 2136 already!?
                    $link['datetime'] = $link['datetime'] / 1000; //convert it!
                
                if ($action != 'linked' 
                    && $action != 'break'
                    && $action != 'linkdecay') 
                    $action = 'deploy';
            }
            else
            {
                $r['class'] = 'fail_object';
                $r['code'] = 500;
                $r['detail'] = 'The provided LinkObject does not appear valid.';
                ob_start();
                    print_r($link);
                $r['debug'] = ob_get_clean();
                return $r;
            }
        }
        
        { // build the query
            $parms = array();
            if (isset($link['user']))
            { // for break or link
                $query = sprintf("INSERT INTO `ingress`.`%s_log` (`guid`, `datetime`, `user`, `portal1`, `portal2`, `region`) VALUES (:guid, :datetime, :user, :portal1, :portal2, :region)
                    ON DUPLICATE KEY UPDATE `datetime`=:datetime, `region`=:region;",
                    $action);
                $parms[] = array(':user',$link['user']);
            }
            else 
            { // for decay (null users)
                $query = sprintf("INSERT INTO `ingress`.`%s_log` (`guid`, `datetime`, `portal1`, `portal2`, `region`) VALUES (:guid, :datetime, :portal1, :portal2, :region)
                    ON DUPLICATE KEY UPDATE `datetime`=:datetime, `region`=:region;",
                    $action);
            }
            $parms[] = array(':guid',$link['guid']);
            $parms[] = array(':datetime',$link['datetime']);
            $parms[] = array(':portal1',$link['portal1']);
            $parms[] = array(':portal2',$link['portal2']);
            $parms[] = array(':region',$link['region']);
                
            $stmt = $db->prepare($query);
            foreach($parms as $parm) {
                $stmt->bindValue($parm[0], $parm[1]);    
            }
        }

        { // execute the insert/update
            try 
            {
                $stmt->execute();
                ob_start();
                    echo "link  :";
                    print_r($link);
                    echo "parms :";
                    print_r($parms);
                    echo "stmt :";
                    print_r($stmt);
                $r['debug'] = ob_get_clean();
                
                if ($stmt->rowCount() > 0) 
                {
                    $r['class'] = 'success';
                    $r['code'] = 201;
                    $r['detail'] = "link:{$action}-log updated";
                } 
                else
                {
                    $r['class'] = 'warn_insert';
                    $r['code'] = 206;
                    $r['detail'] = "link:{$action}-log already exists.";
                }
            }
            catch (PDOException $e)
            {
                $r['class'] = 'fail_query';
                $r['code'] = 500;
                $r['detail'] = $e->getMessage();
            }
        }
        
        return $r;
    }

    function saveControlFieldObject(&$db, $action, $field)
    {
        $r = array('class'=>null,'code'=>null,'details'=>null,'debug'=>null);
        { // validate the object
            if ((isset($field) && is_array($field))
                && (isset($field['guid']) && is_string($field['guid']))
                && (isset($field['portal']) && is_string($field['portal']))
                && (isset($field['mus']) && is_int($field['mus']))
                && (isset($field['datetime']) && is_int($field['datetime']))
                && (isset($field['region']) && is_int($field['region']))
                && (isset($action))
                ) 
            {
                // pass, it's a valid object!
                // now make sure its the style we need:
                if ($field['datetime'] >= 4294967295) // maximum valid datetime in s, so it must be ms... unless it really is the year 2136 already!?
                    $field['datetime'] = $field['datetime'] / 1000; //convert it!
                
                if ($action != 'control' 
                    && $action != 'liberate' 
                    && $action != 'fielddecay') 
                    $action = 'control';
            }
            else
            {
                $r['class'] = 'fail_object';
                $r['code'] = 500;
                $r['detail'] = 'The provided ControlFieldObject does not appear valid.';
                $r['debug'] = $field;
                return $r;
            }
        }
        
        { // build the query
            $parms = array();
            if (isset($link['user']))
            { // for break or link
                $query = sprintf("INSERT INTO `ingress`.`%s_log` (`guid`, `datetime`, `user`, `portal`, `mus`, `region`) VALUES (:guid, :datetime, :user, :portal, :mus, :region)
                    ON DUPLICATE KEY UPDATE `datetime`=:datetime, `region`=:region;",
                    $action);
                $parms[] = array(':user',$link['user']);
            }
            else 
            { // for decay (null users)
                $query = sprintf("INSERT INTO `ingress`.`%s_log` (`guid`, `datetime`, `portal`, `mus`, `region`) VALUES (:guid, :datetime, :portal, :mus, :region)
                    ON DUPLICATE KEY UPDATE `datetime`=:datetime, `region`=:region;",
                    $action);
            }
            $parms[] = array(':guid',$field['guid']);
            $parms[] = array(':datetime',$field['datetime']);
            $parms[] = array(':portal',$field['portal']);
            $parms[] = array(':mus',$field['mus']);
            $parms[] = array(':region',$field['region']);
                
            $stmt = $db->prepare($query);
            foreach($parms as $parm) {
                $stmt->bindValue($parm[0], $parm[1]);    
            }
        }

        { // execute the insert/update
            try 
            {
                $stmt->execute();
                ob_start();
                    echo "field  :";
                    print_r($field);
                    echo "parms :";
                    print_r($parms);
                    echo "stmt :";
                    print_r($stmt);
                $r['debug'] = ob_get_clean();
                
                if ($stmt->rowCount() > 0) 
                {
                    $r['class'] = 'success';
                    $r['code'] = 201;
                    $r['detail'] = "field:{$action}-log updated";
                } 
                else
                {
                    $r['class'] = 'warn_insert';
                    $r['code'] = 206;
                    $r['detail'] = "field:{$action}-log already exists.";
                }
            }
            catch (PDOException $e)
            {
                $r['class'] = 'fail_query';
                $r['code'] = 500;
                $r['detail'] = $e->getMessage();
            }
        }
        
        return $r;
    }
    
    /*elseif ($_GET['table'] == 'pmoddestroy') 
    {
        { // build the query
            $parms = array();
            $query = sprintf("INSERT INTO `ingress`.`%s_log` (`guid`, `datetime`, `user`, `portal`, `mod`, `region`) VALUES (:logid, :datetime, :userid, :portalid, :mod, :region)
                ON DUPLICATE KEY UPDATE `datetime`=:datetime, `mod`=:mod, `region`=:region;",
                $_GET['table']);
            $parms[] = array(':logid',$_GET['logid']);
            $parms[] = array(':datetime',strtotime($_GET['ts']));
            $parms[] = array(':userid',$_GET['user']);
            $parms[] = array(':portalid',$_GET['portal']);
            $parms[] = array(':mod',$_GET['mod']);
            $parms[] = array(':region',$region);
                
            $stmt = $db->prepare($query);
            foreach($parms as $parm) {
                $stmt->bindValue($parm[0], $parm[1]);    
            }
        }
        
        { // execute the update
            
            try 
            {
                $stmt->execute();
                if ($stmt->rowCount() > 0) 
                {
                    header(':', true, 201);
                    echo "<div id=\"success\">\n  <success details=\"pmoddestroy log updated\" />\n</div>\n";
                } 
                else
                {
                    if ($debug)
                    {
                        print_r($parms);
                        print_r($stmt);
                    }                
                    header(':', true, 206);
                    echo "<div id=\"fail_insert\">\n  <error details=\"entry may already exist in its provided state.\" />\n</div>\n";
                }
            }
            catch (PDOException $e)
            {
                header(':', true, 500);
                printf("<div id=\"fail_query\">\n  <error details=\"%s\" />\n</div>\n", $e->getMessage());
                exit();
            }
        }
    
        { // build a pingback object
            $user = getPlayerObject($db, null, $_GET['user']);
            $portal = getPortalObject($db, $_GET['portal']);
            $pingback_object = array('guid'=>$_GET['logid'], 
                'datetime'=>$_GET['ts'], 
                'user'=>$user, 
                'portal'=>$portal,
                'mod'=>$_GET['mod'],
                'region'=>getRegionObject($db,$region));
            $pingback_type = 'pmoddestroy';
        }
    }*/

}

{ // get Objects. will hit the db and provide an object in return.
    function getPlayerObject(&$db, $name=null, $guids=null, $region=null) 
    {
        $users = array();
        { // build the query
            $parms = array();
            $query = "SELECT players.guid, players.name, teams.name as faction, players.region FROM `players`
                LEFT JOIN teams ON players.team=teams.id
                WHERE 1=1";
                
            if (isset($guids)) 
            {
                if (is_array($guids)) 
                {
                    $query .= "\nAND (players.guid = :pid0";
                    $parms[] = array(':pid0',$guids[0]);
                    if (count($guids) > 1) 
                    {
                        for ($n = 1;$n < count($guids);$n++) 
                        {
                            $query .= "\nOR players.guid = :pid{$n}";
                            $parms[] = array(":pid{$n}",$guids[$n]);
                        }
                    }
                    $query .= ")";
                } 
                else 
                {
                    $query .= "\nAND players.guid = :pid";
                    $parms[] = array(':pid',$guids);
                }
            } 
            if (isset($name)) 
            {
                $query .= "\nAND players.name = :pname";
                $parms[] = array(':pname',$name);
            }
            if (isset($region)) 
            {
                $query .= "\nAND region = :regionid";
                $parms[] = array(':regionid',$region);
            }
        }
        
        { // set up the statement / execute
            $stmt = $db->prepare($query);
            foreach($parms as $parm) {
                $stmt->bindValue($parm[0], $parm[1]);    
            }

            try 
            {
                $stmt->execute();
            } 
            catch (PDOException $e)
            {
                return("<div id=\"fail_query\">\n  <error details=\"%s\" />\n</div>\n" % $e->getMessage());
            }
        }
          
        { // populate our player object / return
            if ($stmt->rowCount() > 0) 
            {
                while($row = $stmt->fetch()) {
                    $user = array('guid'=>$row['guid'], 
                        'name'=>$row['name'], 
                        'faction'=>$row['faction'],
                        'region'=>getRegionObject($db, $row['region']));
                    array_push ($users, $user);
                }
                return $users;
            } 
            else 
            {
                return false;
            }
        }
    }

    function getPortalObject(&$db, $guids=null, $region=null) 
    {
        $portals = array();
        $parms = array();
        $query = "SELECT portals.guid, portals.address, portals.latE6, portals.lngE6, portals.name, teams.name as faction, portals.region, portals.lastupdate FROM `portals`
            LEFT JOIN teams ON portals.team=teams.id
            WHERE 1=1";
            
        if (isset($guids)) 
        {
            if (is_array($guids)) 
            {
                $query .= "\nAND (portals.guid = :pid0";
                $parms[] = array(':pid0',$guids[0]);
                if (count($guids) > 1) 
                {
                    for ($n = 1;$n < count($guids);$n++) 
                    {
                        $query .= "\nOR portals.guid = :pid{$n}";
                        $parms[] = array(":pid{$n}",$guids[$n]);
                    }
                }
                $query .= ")";
            } 
            else 
            {
                $query .= "\nAND portals.guid = :pid";
                $parms[] = array(':pid',$guids);
            }
        } 
        if (isset($region)) 
        {
            $query .= "\nAND region = :regionid";
            $parms[] = array(':regionid',$region);
        }

        $stmt = $db->prepare($query);
        foreach($parms as $parm) {
            $stmt->bindValue($parm[0], $parm[1]);    
        }
        // print_r($parms);
        // echo "query=" . $stmt->queryString;

        try 
        {
            $stmt->execute();
        } 
        catch (PDOException $e)
        {
            return("<div id=\"fail_query\">\n  <error details=\"%s\" />\n</div>\n" % $e->getMessage());
        }
          
        if ($stmt->rowCount() > 0) 
        {
            while($row = $stmt->fetch()) 
            {
                $portal = array('guid'=>$row['guid'], 
                    'name'=>$row['name'], 
                    'address'=>$row['address'], 
                    'latE6'=>(int)$row['latE6'], 
                    'lngE6'=>(int)$row['lngE6'], 
                    'name'=>$row['name'], 
                    'faction'=>$row['faction'],
                    'region'=>getRegionObject($db, $row['region']),
                    'lastupdate'=>strtotime($row['lastupdate']),
                    );
                array_push ($portals, $portal);
            }
            return $portals;
        } 
        else 
        {
            return false;
        }
    }

    function getRegionObject(&$db, $guids=null) 
    {
        $regions = array();
        $parms = array();
        $query = "SELECT id, name FROM `regions`
            WHERE 1=1";
        if (isset($guids)) 
        {
            if (is_array($guids)) 
            {
                $query .= "\nAND (id = :id0";
                $parms[] = array(':id0',$guids[0]);
                if (count($guids) > 1) 
                {
                    for ($n = 1;$n < count($guids);$n++) 
                    {
                        $query .= "\nOR id = :id{$n}";
                        $parms[] = array(":id{$n}",$guids[$n]);
                    }
                }
                $query .= ")";
            } 
            else 
            {
                $query .= "\nAND id = :id";
                $parms[] = array(':id',$guids);
            }
        } 
        
        $stmt = $db->prepare($query);
        foreach($parms as $parm) {
            $stmt->bindValue($parm[0], $parm[1]);    
        }

        try 
        {
            $stmt->execute();
        } 
        catch (PDOException $e)
        {
            return("<div id=\"fail_query\">\n  <error details=\"%s\" />\n</div>\n" % $e->getMessage());
        }
          
        if ($stmt->rowCount() > 0) 
        {
            while($row = $stmt->fetch()) 
            {
                $region = array('guid'=>(int)$row['id'], 
                    'name'=>$row['name']);
                array_push ($regions, $region);
            }
            //echo "query=" . $query;
        } 
        else 
        {
            return false;
        }
            return $regions;
    }

    function getChatObject(&$db, $channel=null, $before=null, $after=null, $limit=null, $region=null) 
    {
        $chats = array();
        { // build the query

            $parms = array();
            $query = sprintf("SELECT chat_log.guid, chat_log.datetime, players.guid as user, teams.name AS channel, chat_log.text, chat_log.region, chat_log.secure
                FROM chat_log
                LEFT JOIN players ON chat_log.user = players.guid 
                INNER JOIN teams ON players.team = teams.id
                WHERE 1=1");
            if (isset($channel) && $channel != 0) 
            {
                $query .= "\nAND teams.id = :teamid 
                    AND chat_log.secure = 1";
                $parms[] = array(':teamid',$channel);
            } 
            // else 
            // {
                // $query .= "\nAND chat_log.secure = 0";
            // }
            if (isset($region)) 
            {
                $query .= "\nAND chat_log.region = :regionid";
                $parms[] = array(':regionid',$region);
            }
                
            if (isset($before))
            {
                $query .= "\nAND chat_log.datetime <= :datetime";
                $parms[] = array(':datetime',(int)$before);
            } elseif (isset($after))
            {
                $query .= "\nAND chat_log.datetime >= :datetime";
                $parms[] = array(':datetime',(int)$after);
            }
            $query .= "\nORDER BY datetime desc";
            if (isset($limit) && $limit <= 50)
            {
                $query .= "\nLIMIT 0,:max";
                $parms[] = array(':max',$limit);
            }
            else
            {
                $query .= "\nLIMIT 0,50";
            }
        }
        
        { // set up the statement / execute
            $stmt = $db->prepare($query);
            foreach($parms as $parm) {
                $stmt->bindValue($parm[0], $parm[1]);    
            }
             // print_r($stmt);
             // print_r($parms);
            try 
            {
                $stmt->execute();
            } 
            catch (PDOException $e)
            {
                return("<div id=\"fail_query\">\n  <error details=\"%s\" />\n</div>\n" % $e->getMessage());
            }
        }
          
        { // populate the object / return
            if ($stmt->rowCount() > 0) 
            {
                while($row = $stmt->fetch()) {
                    $chat = array('guid'=>$row['guid'], 
                        'datetime'=>(int)$row['datetime'], 
                        'user'=>getPlayerObject($db, null, $row['user']), 
                        'channel'=>($row['secure'] == 1 ? $row['channel'] : "PUBLIC"),
                        'text'=>$row['text'],
                        'region'=>getRegionObject($db, $row['region']));
                    array_push ($chats, $chat);
                }
                //echo json_encode($chats);
                //echo "query=" . $query;
                return $chats;
            } 
            else 
            {
                return false;
            }
        }
    }

    function getCaptureObject(&$db, $portals=null, $datetime=null, $limit=null)
    {
      $controls = array();
      { // build the query
          $parms = array();
          $query = "SELECT captureLog.guid, captureLog.user, captureLog.portal, captureLog.datetime 
          FROM `capture_log` AS captureLog
          WHERE 1=1";
          if (isset($portals)) 
          {
              if (is_array($portals)) 
              {
                  $query .= "\nAND (captureLog.portal = :portal0";
                  $parms[] = array(':portal0',$portals[0]);
                  if (count($portals) > 1) 
                  {
                      for ($n = 1;$n < count($portals);$n++) 
                      {
                          $query .= "\nOR captureLog.portal = :portal{$n}";
                          $parms[] = array(":portal{$n}",$portals[$n]);
                      }
                  }
                  $query .= ")";
              } 
              else 
              {
                  $query .= "\nAND captureLog.portal = :portal";
                  $parms[] = array(':portal',$portals);
              }
          } 
          if (isset($datetime)) 
          {
              $query .= "\nAND captureLog.datetime <= :datetime";
              $parms[] = array(':datetime',(int)$datetime);
          }
          $query .= "\nORDER BY datetime desc";
          if (isset($limit) && $limit <= 20)
          {
              $query .= "\nLIMIT 0,:max";
              $parms[] = array(':max',$limit);
          }
          else
          {
              $query .= "\nLIMIT 0,10";
          }
      }

      { // set up the statement / execute
          $stmt = $db->prepare($query);
          foreach($parms as $parm) {
              $stmt->bindValue($parm[0], $parm[1]);    
          }
          // print_r($stmt);
          // print_r($parms);
          try 
          {
              $stmt->execute();
          } 
          catch (PDOException $e)
          {
              return("<div id=\"fail_query\">\n  <error details=\"%s\" />\n</div>\n" % $e->getMessage());
          }
      }
      
      { // populate the object / return
          if ($stmt->rowCount() > 0) 
          {
              while($row = $stmt->fetch()) {
                  $resonator = array('guid'=>$row['guid'], 
                      'user'=>getPlayerObject($db, null, $row['user']), 
                      'portals'=>getPortalObject($db, $row['portal']), 
                      'datetime'=>(int)$row['datetime']); 
                  array_push ($controls, $resonator);
              }
              return $controls;
          } 
          else 
          {
              return false; //array('error'=>'no results', 'query'=>$query);
          }
      }
    }
    
    function getResonatorObject(&$db, $table, $portals=null, $datetime=null, $limit=null) 
    {
        if ($table != "destroy") $table = "deploy";
        $resonators = array();
        { // build the query
            $parms = array();
            $query = sprintf("SELECT resonatorLog.guid, resonatorLog.user, resonatorLog.portal, resonatorLog.res, resonatorLog.datetime
                FROM `%s_log` AS resonatorLog
                WHERE 1=1", $table); // Normally i'd say no to this, but its a boolean table value. 
                // it can only be destroy or deploy.
            
            if (isset($portals)) 
            {
                if (is_array($portals)) 
                {
                    $query .= "\nAND (resonatorLog.portal = :portal0";
                    $parms[] = array(':portal0',$portals[0]);
                    if (count($portals) > 1) 
                    {
                        for ($n = 1;$n < count($portals);$n++) 
                        {
                            $query .= "\nOR resonatorLog.portal = :portal{$n}";
                            $parms[] = array(":portal{$n}",$portals[$n]);
                        }
                    }
                    $query .= ")";
                } 
                else 
                {
                    $query .= "\nAND resonatorLog.portal = :portal";
                    $parms[] = array(':portal',$portals);
                }
            } 

            if (isset($datetime)) 
            {
                $query .= "\nAND resonatorLog.datetime <= :datetime";
                $parms[] = array(':datetime',(int)$datetime);
            }
            $query .= "\nORDER BY datetime desc";
            if (isset($limit) && $limit <= 50)
            {
                $query .= "\nLIMIT 0,:max";
                $parms[] = array(':max',$limit);
            }
            else
            {
                $query .= "\nLIMIT 0,50";
            }
        }
        
        { // set up the statement / execute
            $stmt = $db->prepare($query);
            foreach($parms as $parm) {
                $stmt->bindValue($parm[0], $parm[1]);    
            }
            // print_r($stmt);
            // print_r($parms);
            try 
            {
                $stmt->execute();
            } 
            catch (PDOException $e)
            {
                return("<div id=\"fail_query\">\n  <error details=\"%s\" />\n</div>\n" % $e->getMessage());
            }
        }

        { // populate the object / return
            if ($stmt->rowCount() > 0) 
            {
                while($row = $stmt->fetch()) {
                    $resonator = array('guid'=>$row['guid'], 
                        'user'=>getPlayerObject($db, null, $row['user']), 
                        'portal'=>getPortalObject($db, $row['portal']), 
                        'res'=>$row['res'],
                        'datetime'=>(int)$row['datetime']); 
                    array_push ($resonators, $resonator);
                }
                return $resonators;
            } 
            else 
            {
                return false; //array('error'=>'no results', 'query'=>$query);
            }
        }
    }

    function getLinkObject(&$db, $table, $portals=null, $datetime=null, $limit=null) 
    {
        if ($table != 'linked' 
        && $table != 'break'
        && $table != 'linkdecay') 
        $table = 'deploy';

        $links = array();
        { // build the query
            $parms = array();
            $userquery = ($table == 'linkdecay' ? 'null as user' : 'linkLog.user');
            $query = sprintf("SELECT linkLog.guid, %s, linkLog.portal1, linkLog.portal2, linkLog.datetime 
                FROM `%s_log` AS linkLog
                WHERE 1=1", $userquery, $table);
                
            if (isset($portals)) 
            {
                if (is_array($portals)) 
                {
                    $query .= "\nAND ((linkLog.portal1 = :portal0 OR linkLog.portal2 = :portal0)";
                    $parms[] = array(':portal0',$portals[0]);
                    if (count($portals) > 1) 
                    {
                        for ($n = 1;$n < count($portals);$n++) 
                        {
                            $query .= "\nOR (linkLog.portal1 = :portal{$n} OR linkLog.portal2 =  :portal{$n})";
                            $parms[] = array(":portal{$n}",$portals[$n]);
                        }
                    }
                    $query .= ")";
                } 
                else 
                {
                    $query .= "\nAND (linkLog.portal1 = :portal OR linkLog.portal2 = :portal)";
                    $parms[] = array(':portal',$portals);
                }
            } 

            if (isset($datetime)) 
            {
                $query .= "\nAND linkLog.datetime <= :datetime";
                $parms[] = array(':datetime',(int)$datetime);
            }
            $query .= "\nORDER BY datetime desc";
            if (isset($limit) && $limit <= 20)
            {
                $query .= "\nLIMIT 0,:max";
                $parms[] = array(':max',$limit);
            }
            else
            {
                $query .= "\nLIMIT 0,10";
            }
        }
        
        { // set up the statement / execute
            $stmt = $db->prepare($query);
            foreach($parms as $parm) {
                $stmt->bindValue($parm[0], $parm[1]);    
            }
            // print_r($stmt);
            // print_r($parms);
            try 
            {
                $stmt->execute();
            } 
            catch (PDOException $e)
            {
                return("<div id=\"fail_query\">\n  <error details=\"%s\" />\n</div>\n" % $e->getMessage());
            }
        }
          
        { // populate the object / return
            if ($stmt->rowCount() > 0) 
            {
                while($row = $stmt->fetch()) {
                    $link = array('guid'=>$row['guid'], 
                        'user'=>($row['user'] != null ? getPlayerObject($db, null, $row['user']) : null), 
                        'portals'=>getPortalObject($db, array($row['portal1'],$row['portal2'])), 
                        'datetime'=>(int)$row['datetime']); 
                    array_push ($links, $link);
                }
                return $links;
            } 
            else 
            {
                return false; //array('error'=>'no results', 'query'=>$query);
            }
        }
    }

    function getControlFieldObject(&$db, $table, $portals=null, $datetime=null, $limit=null) 
    {
        if ($table != "liberate") {
            $table = "control";
        }
        $fields = array();
        { // build the query
            $parms = array();
            $query = sprintf("SELECT fieldLog.guid, fieldLog.user, fieldLog.portal, fieldLog.mus, fieldLog.datetime 
            FROM `%s_log` AS fieldLog
            WHERE 1=1", $table);
            
            if (isset($portals)) 
            {
                if (is_array($portals)) 
                {
                    $query .= "\nAND (fieldLog.portal = :portal0";
                    $parms[] = array(':portal0',$portals[0]);
                    if (count($portals) > 1) 
                    {
                        for ($n = 1;$n < count($portals);$n++) 
                        {
                            $query .= "\nOR fieldLog.portal = :portal{$n}";
                            $parms[] = array(":portal{$n}",$portals[$n]);
                        }
                    }
                    $query .= ")";
                } 
                else 
                {
                    $query .= "\nAND fieldLog.portal = :portal";
                    $parms[] = array(':portal',$portals);
                }
            } 

            if (isset($datetime)) 
            {
                $query .= "\nAND fieldLog.datetime <= :datetime";
                $parms[] = array(':datetime',(int)$datetime);
            }
            $query .= "\nORDER BY datetime desc";
            if (isset($limit) && $limit <= 20)
            {
                $query .= "\nLIMIT 0,:max";
                $parms[] = array(':max',$limit);
            }
            else
            {
                $query .= "\nLIMIT 0,10";
            }
        }

        { // set up the statement / execute
            $stmt = $db->prepare($query);
            foreach($parms as $parm) {
                $stmt->bindValue($parm[0], $parm[1]);    
            }
            // print_r($stmt);
            // print_r($parms);
            try 
            {
                $stmt->execute();
            } 
            catch (PDOException $e)
            {
                return("<div id=\"fail_query\">\n  <error details=\"%s\" />\n</div>\n" % $e->getMessage());
            }
        }
        
        { // populate the object / return
            if ($stmt->rowCount() > 0) 
            {
                while($row = $stmt->fetch()) {
                    $field = array('guid'=>$row['guid'], 
                        'user'=>getPlayerObject($db, null, $row['user']), 
                        'portals'=>getPortalObject($db, $row['portal']), 
                        'mus'=>$row['mus'], 
                        'datetime'=>(int)$row['datetime']); 
                    array_push ($fields, $field);
                }
                return $fields;
            } 
            else 
            {
                return false; //array('error'=>'no results', 'query'=>$query);
            }
        }
    }

    function getModObject(&$db, $portals=null, $datetime=null, $limit=null) 
    {
        $mods = array();
        { // build the query
            $parms = array();
            $query = "SELECT modLog.guid, modLog.user, modLog.portal, modLog.mod, modLog.datetime
                FROM `pmoddestroy_log` AS modLog
                WHERE 1=1"; 
            
            if (isset($portals)) 
            {
                if (is_array($portals)) 
                {
                    $query .= "\nAND (modLog.portal = :portal0";
                    $parms[] = array(':portal0',$portals[0]);
                    if (count($portals) > 1) 
                    {
                        for ($n = 1;$n < count($portals);$n++) 
                        {
                            $query .= "\nOR modLog.portal = :portal{$n}";
                            $parms[] = array(":portal{$n}",$portals[$n]);
                        }
                    }
                    $query .= ")";
                } 
                else 
                {
                    $query .= "\nAND modLog.portal = :portal";
                    $parms[] = array(':portal',$portals);
                }
            } 

            if (isset($datetime)) 
            {
                $query .= "\nAND modLog.datetime <= :datetime";
                $parms[] = array(':datetime',(int)$datetime);
            }
            $query .= "\nORDER BY datetime desc";
            if (isset($limit) && $limit <= 50)
            {
                $query .= "\nLIMIT 0,:max";
                $parms[] = array(':max',$limit);
            }
            else
            {
                $query .= "\nLIMIT 0,50";
            }
        }
        
        { // set up the statement / execute
            $stmt = $db->prepare($query);
            foreach($parms as $parm) {
                $stmt->bindValue($parm[0], $parm[1]);    
            }
            // print_r($stmt);
            // print_r($parms);
            try 
            {
                $stmt->execute();
            } 
            catch (PDOException $e)
            {
                return("<div id=\"fail_query\">\n  <error details=\"%s\" />\n</div>\n" % $e->getMessage());
            }
        }

        { // populate the object / return
            if ($stmt->rowCount() > 0) 
            {
                while($row = $stmt->fetch()) {
                    $mod = array('guid'=>$row['guid'], 
                        'user'=>getPlayerObject($db, null, $row['user']), 
                        'portal'=>getPortalObject($db, $row['portal']), 
                        'mod'=>$row['mod'],
                        'datetime'=>(int)$row['datetime']); 
                    array_push ($mods, $mod);
                }
                return $mods;
            } 
            else 
            {
                return false; //array('error'=>'no results', 'query'=>$query);
            }
        }
    }

}
?>