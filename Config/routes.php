<?php
/*

	Custom routing for the ResourcesController plugin
	-------------------------------------------------

	@file 		routes.php
	@author 	Gideon Farrell <me@gideonfarrell.co.uk>

	Copyright (c) 2012 Gideon Farrell <http://www.gideonfarrell.co.uk>

*/

Router::connect(
	'/resources/:package/:file/*',
	array('controller'=>'Resources', 'plugin'=>'ResourcesController', 'action'=>'resource', 'compressed'=>true),
	array('package'=>'[A-Za-z0-9\-_\.]+', 'file'=>'[A-Za-z0-9\-_\.]+')
);

Router::connect(
	'/resources/:package/*',
	array('controller'=>'Resources', 'plugin'=>'ResourcesController', 'action'=>'resource', 'compressed'=>true),
	array('package'=>'[A-Za-z0-9\-_\.]+')
);