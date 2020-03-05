<?php 
return array(
	//Set your paypal credential
	'client_id' => 'ATLdhnjCWlK4GnLumIit-mb-ez2V_bcKRIObtSvA0mbQau9ZXStA2vw_sdP_Gaesujpd6_Q1UfFERZlU',
	'secret' => 'EEf67eX_0OZ6n5VxXDaN5gA2-S5WoZCRKPJ4m2S4ot7oO2UgHrGXU4nBI1ed2WCcjEpKjrjEFlWNuLpI',

	//SDK Configuration
	'settings' => array(
		//Available option 'sandbox' or 'live'
		'mode' => 'sandbox',

		//Specify the max request time in seconds
		'http.ConnectionTimeOut' => 30,

		//Whether want to log to a file
		'log.FileName' => storage_path() . '/log/paypal.log',

		/*
		 * Available option 'FINE', 'INFO', 'WARN' or 'ERROR'
		 * Logging is most verbose in the 'FINE' level and decreases as you proceed towards ERROR.
		*/
		'log.logLevel' => 'FINE'
	),
);
