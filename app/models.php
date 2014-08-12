<?php
use Parse\ParseClient; 
ParseClient::initialize($config['PARSE_APP_ID'], $config['PARSE_API_KEY'], $config['PARSE_MASTER_KEY']);