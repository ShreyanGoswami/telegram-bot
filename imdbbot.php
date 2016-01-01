<?php
	require 'botclass.php';
	$bot=new MyTelegramBot();
	
	while(1){
		$msg=$bot->getLatestMsg();
		if(is_null($msg)){
			continue;
		}
		$wrds=explode(' ',$msg['text']);
		$id=$msg['chat']['id'];
		$mid=$msg["message_id"];
		if(($wrds[0]=='/start' || $wrds[0]=='/start'.$bot->getName()) && $bot->isStarted($id)==0){
			if($bot->findUser($id)==0){
				$bot->replyToMsg($id,$mid,"*yawn* just woke up");
				$type=$msg['chat']['type'];
				if($type=="private")
					$title=$msg['chat']['username'];
				else $title=$msg['chat']['title'];
				$bot->addUser($id,$title);
			}else if($bot->findUser($id)==1){
				$bot->replyToMsg($id,$mid,"Bot already running");
			}
				$bot->updateMsg();
		}else if(($wrds[0]=="/stop" || $wrds[0]=="/stop".$bot->getName()) && $bot->isStarted($id)==1){
			if($bot->findUser($id)==0){
				$bot->replyToMsg($id,$mid,"Bot is not running sir");
			}else if($bot->findUser($id)==1){
				$bot->replyToMsg($id,$mid,"*yawn* nap time");
				$bot->updateMsg();
				$bot->removeUser($id);
			}
		}else if($wrds[0]=='/rating' || $wrds[0]=='/rating'.$bot->getName()){
			$mvname="";
			for($i=1;$i<count($wrds);$i++){
				if($i!=count($wrds)-1)
					$mvname.=$wrds[$i]."+";
				else $mvname.=$wrds[$i];
			}
			$info=$bot->fetchMovie($mvname);
			if($bot->findUser($id)==1){
				$str="Title:".$info["Title"]."Rating:".$info['imdbRating']."Plot: ".$info['Plot'];
				$bot->replyToMsg($id,$mid,$str);
			}
			else{
				$bot->replyToMsg($id,$mid,"Please start the bot");
			} 
			$bot->updateMsg();
		}else if($wrds[0]=='/tv' || $wrds[0]=='/tv'.$bot->getName()){
			$ep="";
			$season="";
			$epno="";
			$i=1;
			$j=2;
			for(;$wrds[$i][0]!="-";$i++){
				if($wrds[$i+1][0]!="-")
					$ep.=$wrds[$i]."+";
				else $ep.=$wrds[$i];
			}
			
			for(;($wrds[$i][$j]!="E" && $wrds[$i][$j]!="e") && isset($wrds[$i][$j]);$j++){
				//print "Finding";
				$season.=$wrds[$i][$j];
			}
			
			$j=$j+1;
			for(;isset($wrds[$i][$j]);$j++){
				$epno.=$wrds[$i][$j];
			}
			$json=file_get_contents("http://www.omdbapi.com/?t=".$ep."&Season=".$season."&Episode=".$epno);
			$info=json_decode($json,true);
			if($bot->findUser($id)==1){
				$str="Title:".$info["Title"]."Rating: ".$info["imdbRating"]."Plot: ".$info["Plot"];
				$bot->replyToMsg($id,$mid,$str);
			}
			else{
				$bot->replyToMsg($id,$mid,"Please start the bot");
			} 
			$bot->updateMsg();
			
		}
		$msg=NULL;
	}
?>