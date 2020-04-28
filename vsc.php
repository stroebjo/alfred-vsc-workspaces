<?php

$HOME = $_SERVER['HOME'];
$workspaces_config_dir = $HOME.'/Library/Application Support/Code/User/workspaceStorage/*';
$workspaces = array_filter(glob($workspaces_config_dir), 'is_dir');
$search = ($argc == 2) ? $argv[1] : null;
$items = [];

foreach($workspaces as $dir) {
    $config_file = $dir.'/workspace.json';

    // it seems not all workspaces folders really have a json config.
    if (file_exists($config_file)) {
        $config_str = file_get_contents($config_file);
        $config = json_decode($config_str);

        // spaces are encoded as %20 so we use urldecode
        $workspace_folder = str_replace('file://', '', urldecode($config->folder));
        $workspace_name = basename($workspace_folder);

        if (!empty($search) && stripos($workspace_folder, $search) === false) {
            continue;
        } 

        // make sure the actual workspace still exists
        if (file_exists($workspace_folder)) {
            $items[] = [
                'uid'      => $workspace_folder,
                'arg'      => $workspace_folder,
                'title'    => $workspace_name,
                'type'     => 'file',
                'subtitle' => str_replace($_SERVER['HOME'], '~', $workspace_folder),
                'text' => [
                    'copy' => $workspace_folder,
                ],
                'mods' => [
                    'cmd' => [
                        'subtitle' => 'Open in terminal',
                        'arg' => $workspace_folder
                    ]
                ]
            ];
        }
    }
}

echo json_encode(['items' => $items]);
