<?php

class StaticPages
{
    private $path_to_sites_directory = __DIR__ . DIRECTORY_SEPARATOR . "sites";

    public function getSites()
    {
        if( ! is_dir( $this->path_to_sites_directory ) )
            return;

        $sites_dir_items = scandir( $this->path_to_sites_directory );

        $sites = array();

        foreach( $sites_dir_items as $sites_dir_item )
        {
            if( $sites_dir_item == "." || $sites_dir_item == ".." )
                continue;
            
            $path_to_dir_item = $this->path_to_sites_directory . DIRECTORY_SEPARATOR . $sites_dir_item;

            if( ! is_dir( $path_to_dir_item ) )
                continue;
            
            $path_to_config_file = $path_to_dir_item . DIRECTORY_SEPARATOR . "config.json";

            if( ! is_file( $path_to_config_file ) )
                continue;
            
            $site_config = file_get_contents( $path_to_config_file );
            $site_config = json_decode( $site_config, true );

            if( ! is_array( $site_config ) || count( $site_config ) < 1 )
                continue;

            $required_config_keys = array( "name", "description", "input_dir_path", "output_dir_path" );

            foreach( $required_config_keys as $required_config_key )
            {
                if( ! array_key_exists( $required_config_key, $site_config ) )
                    continue( 2 );
            }

            $sites[ $sites_dir_item ] = $site_config;
        }

        return $sites;
    }

    public function createSite( String $id, String $name, String $description, String $input_dir_path, String $output_dir_path )
    {
        $site_dir_path = $this->path_to_sites_directory . DIRECTORY_SEPARATOR . $id;

        if( is_dir( $site_dir_path ) )
            return false;
        
        mkdir( $site_dir_path );

        $site_config['name'] = $name;
        $site_config['description'] = $description;
        $site_config['input_dir_path'] = $input_dir_path;
        $site_config['output_dir_path'] = $output_dir_path;

        $path_to_config_file = $site_dir_path . DIRECTORY_SEPARATOR . "config.json";

        $json_encoded_config = json_encode( $site_config );

        file_put_contents( $path_to_config_file, $json_encoded_config );

        return is_file( $path_to_config_file ) && file_get_contents( $path_to_config_file ) == $json_encoded_config;
    }

    public function editSiteConfig( String $id, String $name, String $description, String $input_dir_path, String $output_dir_path )
    {
        $site_dir_path = $this->path_to_sites_directory . DIRECTORY_SEPARATOR . $id;

        if( ! is_dir( $site_dir_path ) )
            return false;
        
        $path_to_config_file = $site_dir_path . DIRECTORY_SEPARATOR . "config.json";

        $json_encoded_config = file_get_contents( $path_to_config_file );
        
        $site_config = json_decode( $json_encoded_config, true );

        $site_config['name'] = $name;
        $site_config['description'] = $description;
        $site_config['input_dir_path'] = $input_dir_path;
        $site_config['output_dir_path'] = $output_dir_path;

        $json_encoded_config = json_encode( $site_config );

        file_put_contents( $path_to_config_file, $json_encoded_config );

        return is_file( $path_to_config_file ) && file_get_contents( $path_to_config_file ) == $json_encoded_config;
    }
}

$staticPages = new StaticPages;

if( $argc > 0 )
{
    $action = null;

    if( basename( __FILE__ ) == $argv[ 0 ] )
    {
        unset( $argv[ 0 ] );
        $argv = array_values( $argv );
    }

    if( isset( $argv[ 0 ] ) )
        $action = $argv[ 0 ];
    
    switch( $action )
    {
        case "GetSites":
            print_r( $staticPages->getSites() );
            break;
        case "CreateSite":
            if( ! count( $argv ) >= 6 )
                die( "Error: TooFewArgs" );
            
            $site_id = $argv[ 1 ];
            $site_name = $argv[ 2 ];
            $site_description = $argv[ 3 ];
            $site_input_dir_path = $argv[ 4 ];
            $site_output_dir_path = $argv[ 5 ];

            echo $staticPages->createSite( $site_id, $site_name, $site_description, $site_input_dir_path, $site_output_dir_path ) ? "Site Created Successfully!" : "Site Creation Failed!";
            break;
        case "EditSiteConfig":
            if( ! count( $argv ) >= 6 )
                die( "Error: TooFewArgs" );
            
            $site_id = $argv[ 1 ];
            $site_name = $argv[ 2 ];
            $site_description = $argv[ 3 ];
            $site_input_dir_path = $argv[ 4 ];
            $site_output_dir_path = $argv[ 5 ];

            echo $staticPages->editSiteConfig( $site_id, $site_name, $site_description, $site_input_dir_path, $site_output_dir_path ) ? "Site Config Edited Sucessfully!" : "Site Config Edit Failed!";
            break;
        default:
            if( ! is_null( $action ) )
                echo "Unknown Action: " . $action;
            break;
    }
}
