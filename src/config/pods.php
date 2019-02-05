<?php

$configs  = [];
$versions = new DirectoryIterator(base_path().'/pods');
foreach ($versions as $versionDir) {
    if ($versionDir->isDir() && !$versionDir->isDot()) {
        $version = $versionDir->getFilename();
        $pods    = new DirectoryIterator(base_path().'/pods/'.$version);
        foreach ($pods as $podDir) {
            if ($podDir->isDir() && !$podDir->isDot()) {
                $pod = $podDir->getFilename();
                foreach (glob(base_path().'/pods/'.$version.'/'.$pod.'/Config/*.php') as $podCfg) {
                    $configs[strtolower($version)][strtolower($pod)][basename($podCfg, '.php')] = include $podCfg;
                }
            }
        }
    }
}
return $configs;
