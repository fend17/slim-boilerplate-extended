<?php 

class ConfigHandler
{
    public static function getConfig()
    {
        // Used only when deploying to Heroku
        $databaseUrl = getenv('CLEARDB_DATABASE_URL');
        if ($databaseUrl) {
            return Self::parseDatabaseURL($databaseUrl);
        } else {
            return Self::getDefaultConfig();
        }
    }
    public static function parseDatabaseURL($url)
    {
        $url = parse_url($url);
        $config = [];
        $config['db']['host'] = $url['host'];
        $config['db']['user'] = $url['user'];
        $config['db']['pass'] = $url['pass'];
        $config['db']['dbname'] = substr($url['path'], 1);
        $config['displayErrorDetails'] = true;
        $config['addContentLengthHeader'] = false;
        return $config;
    }
    public static function getDefaultConfig()
    {
        $config = [];
        $config['displayErrorDetails'] = true;
        $config['addContentLengthHeader'] = false;
        $config['db']['host']   = 'localhost:8889';
        $config['db']['user']   = 'root';
        $config['db']['pass']   = 'root';
        $config['db']['dbname'] = 'todos';
        return $config;
    }
}
