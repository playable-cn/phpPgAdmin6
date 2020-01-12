<?php return array (
  0 => 
  array (
    'GET' => 
    array (
      '/status' => 'route0',
      '/redirect' => 'route2',
      '/' => 'route5',
      '' => 'route6',
    ),
    'POST' => 
    array (
      '/redirect/server' => 'route1',
    ),
  ),
  1 => 
  array (
    'GET' => 
    array (
      0 => 
      array (
        'regex' => '~^(?|/redirect/([^/]+)|/src/views/([^/]+)()|/(\\w+)()()|/(.*)()()())$~',
        'routeMap' => 
        array (
          2 => 
          array (
            0 => 'route2',
            1 => 
            array (
              'subject' => 'subject',
            ),
          ),
          3 => 
          array (
            0 => 'route3',
            1 => 
            array (
              'subject' => 'subject',
            ),
          ),
          4 => 
          array (
            0 => 'route4',
            1 => 
            array (
              'subject' => 'subject',
            ),
          ),
          5 => 
          array (
            0 => 'route6',
            1 => 
            array (
              'path' => 'path',
            ),
          ),
        ),
      ),
    ),
    'POST' => 
    array (
      0 => 
      array (
        'regex' => '~^(?|/src/views/([^/]+))$~',
        'routeMap' => 
        array (
          2 => 
          array (
            0 => 'route3',
            1 => 
            array (
              'subject' => 'subject',
            ),
          ),
        ),
      ),
    ),
  ),
);