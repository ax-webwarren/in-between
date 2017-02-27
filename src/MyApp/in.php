<?php
/**
WTD fvck inc
**/
?>
<?php
/**
WTD
**/

namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class In implements MessageComponentInterface {
    protected $clients;	
	var $num1; 	
	var $num2;
	var $num3;
	var $p;
	var $b;
	var $ef;
	var $client_list;
	var $client_name;
	var $clients_confirm;
	var $turn;
	var $reply;
	var $start;
	var $cw;
	var $cl;
	var $statlist;
	var $k;
	var $d;

	function randomnum($reset) {		
		if ((!$this->num1) || ($reset == true)) {
			$this->num1 = rand(1,13);
			$this->num2 = rand(1,13);
			$this->num3 = rand(1,13);
			
			#$this->num1 = min($this->num1,$this->num2,$this->num3);
			#$this->num3 = max($this->num1,$this->num2,$this->num3);
			
			if ($this->num1 == $this->num3 || ($this->num1 == $this->num3 + 1) || ($this->num3 == $this->num1 + 1)) {	
				$this->randomnum(true);	
			}
		}
	}
	
	function nextTurn($maxNumber) {		
		//NEXT Turn
		if ($this->turn != $maxNumber) {
			$this->turn++;
		}
		else {
			$this->turn = 0;
		}
		
		return $this->turn;
	}
	
	function next($maxNumber) {
		//NEXT Turn
		$i = $this->turn;
		if ($this->turn != $maxNumber) {
			$i++;
		}
		else {
			$i = 0;
		}
		
		return $i;
	}
	
	function prev($maxNumber) {
		//NEXT Turn
		$i = $this->turn;
		if ($this->turn != 0) {
			$i--;
		}
		else {
			$i = $maxNumber;
		}
		
		return $i;
	}
	
	function status($stat,$amo,$client_id,$client_list) {
		$status = "";
		$i = 0;
		$numRecv = count($this->clients) - 1;
		foreach($client_list as $client) {
			if ($client == $client_id) {
				if ($stat == "w") {
					$this->cw[$client] += $amo;
					$this->statlist[$client] = $amo."W";
				}
				elseif ($stat == "d") {
					$this->statlist[$client] = "<span class='current_turn'>D</span>";
				}
				elseif ($stat == "p") {
					$this->statlist[$client] = "<span class='pass'>P</span>";
				}
				elseif ($stat == "o") {
					#$this->statlist[$client] = "O";
				}
				elseif ($stat == "l") {
					$this->cl[$client] += $amo;
					$this->statlist[$client] = $amo."L";
				}	
				elseif ($stat == "k") {
					$this->statlist[$client] = "<span class='confirm'>K</span>";
				}	
				elseif ($stat == "r") {
					$this->statlist[$client] = "<span class='restart'>R</span>";		
				}
			}					
			if ($i == $this->next($numRecv)) {
				if ($stat != "k" && $stat != "p") {
					$n = "<span class='next'>N</span>";
				}
				elseif ($stat == "p") {
					$n = "<span class='current_turn'>D</span>";
				}
			}
			else {
				$n = "<span class='next'></span>";			
			}			
			var_dump($this->client_name);
			$status .= "<div class='playerStatusindex'><span class='index'>{$i}</span> <span class='index-name'>{$this->client_name[$client]}</span> : <span class='clientIncome'>+{$this->cw[$client]}</span> <span class='clientExpense'>-{$this->cl[$client]}</span> <span class='clientAction'>{$this->statlist[$client]} {$n}</span> </div> ";
			$i++;
		}
		return $status;
	}
	
    public function __construct() {
        $this->clients = new \SplObjectStorage;
		$this->client_list = array();
		$this->client_name = array();
		$this->statlist = array();
		$this->cw = array();
		$this->cl = array();
		$this->clients_confirm = array();
		$this->turn = 0;
		$this->randomnum(false);		
		$this->start = false;
		$this->k = true;
		$this->s = false;
		$this->p = 0;
		$this->d = false;
		$this->removed_id = 0;
		$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
    }	

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
		if (!$this->start) {
			$this->clients->attach($conn);		
			
			if (!in_array($conn->resourceId, $this->client_list)) {
				if ($this->client_list[$this->removed_id] != 0) {
					array_push($this->client_list, $conn->resourceId);				
				}
				else {
					$this->client_list[$this->removed_id] = $conn->resourceId;					
					$removed_id = array_keys($this->client_list,$conn->resourceId);
					$this->removed_id = $removed_id[0];
				}
			}				
				
			$i = 0;
			foreach($this->clients as $client) {
				$this->stat_list = $this->status("o","",$this->client_list[$i],$this->client_list);		
				$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";					
				if ($client->resourceId == $this->client_list[$this->turn]) {
					$this->reply .= "<div class='turny'>Your Turn</div>";
				}
				else {
					$this->reply .= "<div class='turnw'>Wait</div>";
				}
					$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
					$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
					$client->send($this->reply);
					$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
					$i++;
			}	
			echo "New connection! ({$conn->resourceId})\n";	
		}
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');		
		
		if (!$this->start) {
			//GET USER NAME			
			$this->stat_list = $this->status("o",$msg,$from->resourceId,$this->client_list);
			//IF USERNAME IS NOT YET REGISTERED
			if (!intval($msg) && !in_array($msg,$this->client_name) && $msg != "K") {
				$this->client_name[$from->resourceId] = $msg;
				var_dump($this->client_name[$from->resourceId]);
				foreach($this->clients as $client) {						
					if ($client->resourceId == $this->client_list[$this->turn]) {						
						$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
						$this->reply .= "<div class='turny'>Your Turn</div>";
						$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
						$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
						$client->send($this->reply);
						$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
					}
					else {
						$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
						$this->reply .= "<div class='turnw'>Wait</div>";
						$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
						$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
						$client->send($this->reply);
						$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
					}	
				}
			}
			//IF USERNAME IS REGISTERED
			elseif (in_array($msg,$this->client_name)) {
				foreach($this->clients as $client) {						
					if ($client->resourceId == $this->client_list[$this->turn]) {
						$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> <span class='status'>NAME TAKEN</span></div>";
						$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
						$this->reply .= "<div class='turny'>Your Turn</div>";
						$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
						$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list} <br/></div>";
						$client->send($this->reply);
						$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
					}
					else {
						$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> <span class='status'>NAME TAKEN</span></div>";
						$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
						$this->reply .= "<div class='turnw'>Wait</div>";
						$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
						$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
						$client->send($this->reply);
						$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
					}	
				}
			}

			//Confirm
			elseif ($msg == "K") {		
				//Insert per user input
				if ($this->k) {
					if ($from->resourceId == $this->client_list[$this->turn]) {
						$this->s = true;		
						if (!in_array($from->resourceId, $this->clients_confirm)) {
							array_push($this->clients_confirm, $from->resourceId);					
						} else {
							if (count($this->client_list) == count($this->clients_confirm)) {								
								#$this->nextTurn($numRecv);	
							}
							#unset($this->clients_confirm);
							#$this->clients_confirm = array();
							#array_push($this->clients_confirm, $from->resourceId);					
						}

						if (count($this->client_list) == count($this->clients_confirm)) {							
							unset($this->clients_confirm);
							$this->clients_confirm = array();
							$this->start = true;	
							$this->k = false;
							$this->nextTurn($numRecv);
							$this->randomnum(true);		
							foreach($this->clients as $client) {
								if ($client->resourceId == $this->client_list[$this->turn]) {
									$this->stat_list = $this->status("k",$msg,$from->resourceId,$this->client_list);						
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
									$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
									$this->reply .= "<div class='turny'>Your Turn</div>";										
									$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
									$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
									$client->send($this->reply);
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
								}							
								elseif ($client->resourceId == $from->resourceId) {								
									$this->stat_list = $this->status("k",$msg,$from->resourceId,$this->client_list);						
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
									$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
									$this->reply .= "<div class='turnw'>Wait</div>";										
									$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
									$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
									$client->send($this->reply);
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
								}
								else {
									$this->stat_list = $this->status("k",$msg,$from->resourceId,$this->client_list);						
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
									$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
									$this->reply .= "<div class='turnw'>Wait</div>";										
									$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
									$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
									$client->send($this->reply);
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
								}
							}						
						}
						else {	
							$this->nextTurn($numRecv);														
							foreach($this->clients as $client) {
								if ($client->resourceId == $this->client_list[$this->turn]) {
									$this->stat_list = $this->status("k",$msg,$from->resourceId,$this->client_list);						
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
									$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
									$this->reply .= "<div class='turny'>Your Turn</div>";										
									$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
									$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
									$client->send($this->reply);
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
								}
								else {
									$this->stat_list = $this->status("k",$msg,$from->resourceId,$this->client_list);						
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
									$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
									$this->reply .= "<div class='turnw'>Wait</div>";										
									$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
									$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
									$client->send($this->reply);
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
								}											
							}
						}
					}
				}	
			}			
				
			else {
				//ADDING OF BET			
				if ($from->resourceId == $this->client_list[0]) {
					$this->p += $msg;
					foreach($this->clients as $client) {
						if ($client->resourceId == $this->client_list[$this->turn]) {
							$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
							$this->reply .= "<div class='turny'>Your Turn</div>";
							$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
							$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
							$client->send($this->reply);
							$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
						}
						else {
							$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
							$this->reply .= "<div class='turnw'>Wait</div>";
							$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
							$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
							$client->send($this->reply);
							$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
						}			
					}
				}
			}
				
		}
		
		if ($this->p == 0) {
			//Restart 
			if ($msg == "R") {		
				//Insert per user input
				//if ($this->k) {
					if ($from->resourceId == $this->client_list[$this->turn]) {
						$this->s = true;
						if (!in_array($from->resourceId, $this->clients_confirm)) {
							array_push($this->clients_confirm, $from->resourceId);							
						} else {
							if (count($this->client_list) == count($this->clients_confirm)) {								
								#$this->nextTurn($numRecv);	
							}
							#unset($this->clients_confirm);
							#$this->clients_confirm = array();
							#array_push($this->clients_confirm, $from->resourceId);					
						}

						if (count($this->client_list) == count($this->clients_confirm)) {	
							$this->p = 0;
							$this->cw = array();
							$this->cl = array();							
							unset($this->clients_confirm);
							$this->clients_confirm = array();								
							$this->turn = 0;	
							$this->start = false;
							$this->k = true;
							$this->s = false;
							$this->d = false;
							$this->removed_id = 0;
							foreach($this->clients as $client) {
								if ($client->resourceId == $this->client_list[$this->turn]) {
									$this->stat_list = $this->status("r",$msg,$from->resourceId,$this->client_list);						
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
									$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
									$this->reply .= "<div class='turny'>Your Turn</div>";										
									$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
									$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
									$client->send($this->reply);
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
								}							
								elseif ($client->resourceId == $from->resourceId) {								
									$this->stat_list = $this->status("r",$msg,$from->resourceId,$this->client_list);						
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
									$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
									$this->reply .= "<div class='turnw'>Wait</div>";										
									$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
									$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
									$client->send($this->reply);
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
								}
								else {
									$this->stat_list = $this->status("r",$msg,$from->resourceId,$this->client_list);						
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
									$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
									$this->reply .= "<div class='turnw'>Wait</div>";										
									$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
									$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
									$client->send($this->reply);
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
								}
							}						
						}
						else {	
							$this->nextTurn($numRecv);														
							foreach($this->clients as $client) {
								if ($client->resourceId == $this->client_list[$this->turn]) {
									$this->stat_list = $this->status("r",$msg,$from->resourceId,$this->client_list);						
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
									$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
									$this->reply .= "<div class='turny'>Your Turn</div>";										
									$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
									$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
									$client->send($this->reply);
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
								}
								else {
									$this->stat_list = $this->status("r",$msg,$from->resourceId,$this->client_list);						
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
									$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
									$this->reply .= "<div class='turnw'>Wait</div>";										
									$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
									$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
									$client->send($this->reply);
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
								}											
							}
						}
					}
				//}
			}
		}
			
		if ($this->p > 0 && $this->start) {
			foreach ($this->clients as $client) {	
				//IF Client's Turn
				if ($client->resourceId == $this->client_list[$this->turn]) {
					//GET INPUT FROM PLAYER
					if (intval($msg) > 0) {
							//IF Confirmation came from client's turn
							//ADDING OF BET
							if ($from->resourceId == $this->client_list[$this->turn]) {
								if ($this->start && $this->s) {
									//IF client is in range
									if (($this->num2 > min($this->num1,$this->num2,$this->num3)) && ($this->num2 < max($this->num1,$this->num2,$this->num3))) {											
											if ($msg < $this->p) {
												$this->p -= $msg;
											}
											else {
												$msg = $this->p;
												$this->p -= $this->p;
											}
											$this->stat_list = $this->status("w",$msg,$this->client_list[$this->turn],$this->client_list);	
											foreach ($this->clients as $client) {											
											//Send Message to All											
											
											if ($client->resourceId == $this->client_list[$this->turn]) {	
												$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'>{$this->num2}</span> <span class='num3'>{$this->num3}</span> <span class='status'>WIN</span></div>";
												$this->reply .= "<div class='turnw'>Wait</div>";
												//$this->status("w",$msg);
											}
											elseif ($client->resourceId == $this->client_list[$this->next($numRecv)]) {												
												$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'>{$this->num2}</span> <span class='num3'>{$this->num3}</span> <span class='status'>WIN</span></div>";
												$this->reply .= "<div class='turny'>Your Turn</div>";
											}
											else {										
												$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'>{$this->num2}</span> <span class='num3'>{$this->num3}</span> <span class='status'>WIN</span></div>";
												$this->reply .= "<div class='turnw'>Wait</div>";
												//$this->status("w",$msg);
											}
											if ($this->p == 0) {
												$this->reply .= "<span class='game-over' style='display: none'>GAMEOVER</span>";
											}
											
											$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";									
											$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
											$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
											$client->send($this->reply);
											$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";											
										}																		
									}
									else {					
										if ($msg < $this->p) {					
											$this->p += $msg;
										}
										else {
											$msg = $this->p;
											$this->p += $this->p;
										}
										$this->stat_list = $this->status("l",$msg,$this->client_list[$this->turn],$this->client_list);	
										foreach ($this->clients as $client) {										
											//Send Message to All		
											if ($client->resourceId == $this->client_list[$this->turn]) {											
												$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'>{$this->num2}</span> <span class='num3'>{$this->num3}</span> <span class='status'>LOSE</span></div>";
												$this->reply .= "<div class='turnw'>Wait</div>";
											}
											elseif ($client->resourceId == $this->client_list[$this->next($numRecv)]) {												
												$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'>{$this->num2}</span> <span class='num3'>{$this->num3}</span> <span class='status'>LOSE</span></div>";
												$this->reply .= "<div class='turny'>Your Turn</div>";
											}
											else {											
												$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'>{$this->num2}</span> <span class='num3'>{$this->num3}</span> <span class='status'>LOSE</span></div>";
												$this->reply .= "<div class='turnw'>Wait</div>";
											}		
											$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";											
											$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
											$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
											$client->send($this->reply);
											$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
										}		
									}
									
									//Next Turn 
									$this->nextTurn($numRecv);	
									$this->d = true;				
								}
							}
					}
					//PASS
					if ($msg == "P") {					
						//IF Confirmation came from client's turn
						if ($from->resourceId == $this->client_list[$this->turn] && $this->start) {
							//Next Turn 
							$this->randomnum(true);
							$this->stat_list = $this->status("p",$msg,$this->client_list[$this->turn],$this->client_list);													
							$this->nextTurn($numRecv);
							foreach($this->clients as $client) {
								if ($client->resourceId == $this->client_list[$this->turn]) {
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
									$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
									$this->reply .= "<div class='turny'>Your Turn</div>";									
									$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
									$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
									$client->send($this->reply);
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
								}
								else {
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
									$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
									$this->reply .= "<div class='turnw'>Wait</div>";									
									$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
									$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
									$client->send($this->reply);
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
								}											
							}
						}
					}			
				} else {
					
				}
				
				//DEAL
				if ($msg == "D") {					
					if ($this->d) {
						if ($client->resourceId == $this->client_list[$this->turn]) {
							$this->randomnum(true);
							$this->stat_list = $this->status("d",$msg,$this->client_list[$this->turn],$this->client_list);							
							
							foreach($this->clients as $client) {					
								if ($client->resourceId == $this->client_list[$this->turn]) {						
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
									$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
									$this->reply .= "<div class='turny'>Your Turn</div>";									
									$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
									$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
									$client->send($this->reply);
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
								}
								else {
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
									$this->reply .= "<div class='p'><span class='pMannyLabel'>pot:</span> <span class='pManny'>{$this->p}</span></div>";
									$this->reply .= "<div class='turnw'>Wait</div>";									
									$this->reply .= "<div class='y'>you: " . "<span class='clientIndexNum'>" . array_search($client->resourceId,$this->client_list) . "</span> <span class='clientName'>{$this->client_name[$from->resourceId]}</span> </div>";
									$this->reply .= "<div class='status'><span class='stat_label'>stat:</span> <br/>{$this->stat_list}</div>";
									$client->send($this->reply);
									$this->reply = "<div class='randNum'><span class='num1'>{$this->num1}</span> <span class='num2'> </span> <span class='num3'>{$this->num3}</span> </div>";
								}					
							}						
							$this->d = false;
						}						
						if ($this->start) {
							$this->k = false;
						}
					}
				}				
				
			}
		}
	}	
    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
		//Clear List of Players
		$removed_id = array_keys($this->client_list,$conn->resourceId);
		$this->removed_id = $removed_id[0];
		unset($this->client_list[$this->removed_id]);
		var_dump($this->client_list);
		#$this->client_list = array();
				
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}