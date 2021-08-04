<?php

require_once('Websend.php');

function GameServerInfo($IP = '198.12.123.133', $Port = 25565, $Timeout = 8) {

    $Socket = Socket_Create( AF_INET, SOCK_STREAM, SOL_TCP );
    Socket_Set_Option( $Socket, SOL_SOCKET, SO_SNDTIMEO, array( 'sec' => $Timeout, 'usec' => 0 ) );
    if( $Socket === false || @Socket_Connect( $Socket, $IP, (int)$Port ) === false )
        return false;
    if(Socket_Send( $Socket, "\xFE", 1, 0 ) === FALSE){
        return false;
    }
	try{
		$Len = Socket_Recv( $Socket, $Data, 256, 0 );
		Socket_Close( $Socket );
		if( $Len < 4 || $Data[ 0 ] != "\xFF" )
			return false;
		$Data = SubStr( $Data, 3 );
		$Data = iconv( 'UTF-16BE', 'UTF-8', $Data );
		$Data = Explode( "\xA7", $Data );
		
		return Array('motd' => SubStr( $Data[ 0 ], 0, -1 ), 'Players' => isset( $Data[ 1 ] ) ? IntVal( $Data[ 1 ] ) : 0, 'MaxPlayers' => isset( $Data[ 2 ] ) ? IntVal( $Data[ 2 ] ) : 0);
	}catch(Exception $e){
		return false;
	}
    
}

function SendServerCommand($Command){
    
    $ws = new Websend("198.12.123.133", 8542);
    $ws->connect("uUxqc31Hqs");
    $ws->doCommandAsConsole($Command);
    $ws->disconnect();
    
}

?>