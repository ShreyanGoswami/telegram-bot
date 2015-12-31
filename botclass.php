<?php	
	class MyTelegramBot{
		private $website="https://api.telegram.org/bot<token>";
		private $name="@gtxtestbot";
		private $count=0;
		private $userlist;
		private $flags;
		private $startTxt="*yawns* just woke up";
		
		//obtain all the messages recieved by the bot that is stored in the cloud
		function getUpd(){
			$update=file_get_contents($this->website."/getupdates");
			$updateArray=json_decode($update,true);
			return $updateArray;
		}
		
		//send a message text to chatid
		function sendMsg($id,$text){
			file_get_contents($this->website."/sendmessage?chat_id=".$id."&text=".$text);
		}
		
		//replies to a message sent to bot
		function replyToMsg($id,$msgid,$text){
			file_get_contents($this->website."/sendmessage?chat_id=".$id."&text=".$text."&reply_to_message_id=".$msgid);
		}
		
		//get the last message recieved by the bot
		function getLatestMsg(){
			$updateArray=$this->getUpd();
			$len=count($updateArray['result']);
			if($len>0)
			{
				$msg=$updateArray["result"][$len-1]["message"];
				//var_dump($msg);
				return $msg;
			}
		}
		
		function updateMsg(){
			$update=$this->getUpd();
			$len=count($update['result']);
			if($len>0){
				$updid=$update["result"][$len-1]["update_id"]+1;
				file_get_contents($this->website."/getupdates?offset=".$updid);
			}
		}
		
		function setBotKey($token){
			$this->token=$key;
		}
		
		function setName($name){
			$this->name=$name;
		}
		
		function getStartMsg(){
			return $this->startTxt;
		}
		
		function getName(){
			return $this->name;
		}
		
		function fetchMovie($movie){
			$json=file_get_contents("http://www.omdbapi.com/?t=".$movie."&y=&plot=full&r=json");
			$info=json_decode($json,true);
			return $info;
		}
		
		function addUser($id,$title){
			if($this->findUser($id)==0){
				$this->userlist[$id]=$title;
				$this->flags[$id]=1;
			}
		}
		
		function isStarted($id){
			return $this->flags[$id];
		}
		
		function removeUser($id){
			if($this->findUser($id)==1){
				unset($this->userlist[$id]);
				$this->flags[$id]=0;
				$this->userlist=array_values($this->userlist);
			}
		}
		
		function findUser($id){
			if(isset($this->userlist[$id])){
				return 1;
			}else return 0;
		}
	}
?>