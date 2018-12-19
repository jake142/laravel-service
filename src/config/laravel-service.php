<?php

$configs = [];
$versions = new DirectoryIterator(base_path().'/Services');
foreach ($versions as $versionDir) {
    if ($versionDir->isDir() && !$versionDir->isDot()) {
        $version =  $versionDir->getFilename();
        $services = new DirectoryIterator(base_path().'/Services/'.$version);
       	foreach ($services as $serviceDir) {
       		if ($serviceDir->isDir() && !$serviceDir->isDot()) {
       			$service =  $serviceDir->getFilename();
		        foreach (glob(base_path().'/Services/'.$version.'/'.$service.'/config/*.php') as $serviceCfg) {
		        	$configs[strtolower($version)][strtolower($service)][basename($serviceCfg,'.php')] = include $serviceCfg;
		        }
       		}
       	}
    }
}
return $configs;
