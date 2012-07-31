<?php

    require_once __DIR__.'/../vendor/autoload.php';

    function createImage($hipinteger, $instagrammed = false) {
        
        $font = __DIR__.'/../fonts/Neue Haas Grotesk Display W02 Medium.ttf';
        
        if (!file_exists($font)) {
            $font = __DIR__.'/../fonts/Arial.ttf';
        }
        
        if ($instagrammed) {
            $im = imagecreatefrompng('images/wood_instagrammed.png');
        } else {
            $im = imagecreatefrompng('images/wood612.png');
        }
        
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        $bbox = imagettfbbox(72, 0, $font, $hipinteger);
        imagettftext($im, 72, 0, floor((611 - $bbox[4] - $bbox[0]) / 2), floor((611 - $bbox[5] - $bbox[1]) / 2), $black, $font, $hipinteger);

        if ($filename == null) {

            header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
            header('Content-Type: image/png');
            imagepng($im);
            
        } else {
            
            imagepng($im, $filename, 1);
            
        }

        imagedestroy($im);        
        
    }

    $app = new Silex\Application();

    // $app['debug'] = true;

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/views',
    ));

    $app->get('/integer/brooklyn/get/', function () use ($app) {
        
        $browser = new Buzz\Browser();
        $response = $browser->submit('http://api.brooklynintegers.com/rest/', array('method' => 'brooklyn.integers.create'));
        $body = $response->getContent();
        $data = json_decode($body);

        return $data->integer;
        
    });

    $app->get('/integer/mission/get/', function () use ($app) {
        
        $browser = new Buzz\Browser();
        $response = $browser->submit('http://www.missionintegers.com/next-int', array('format' => 'json'));
        $body = $response->getContent();
        $data = json_decode($body);

        return $data->number;
        
    });

    $app->get('/create/{brooklyn_integer}/{mission_integer}', function ($brooklyn_integer, $mission_integer) use ($app) {
        
        return $app['twig']->render('combine.twig.html', array(
            'brooklyn_integer' => $brooklyn_integer,
            'mission_integer' => $mission_integer,
            'hipinteger' => $mission_integer + $brooklyn_integer,
        ));
        
    })
    ->assert('brooklyn_integer', '\d+')
    ->assert('mission_integer', '\d+');

    $app->get('/filter/get/{hipinteger}', function ($hipinteger) use ($app) {

        createImage($hipinteger, true);

    })
    ->assert('hipinteger', '\d+');

    $app->get('/image/get/{hipinteger}', function ($hipinteger) use ($app) {

        createImage($hipinteger);

    })
    ->assert('hipinteger', '\d+');

    $app->get('/', function () use ($app) {
        
        return $app['twig']->render('index.twig.html');
    
    });
    
    $app->get('/render/{hipinteger}', function ($hipinteger) use ($app) {
        
        return $app['twig']->render('render.twig.html', array(
            'hipinteger' => $hipinteger
        ));
        
    });    

    $app->get('/dispatch', function () use ($app) {
    
        return $app['twig']->render('dispatch.twig.html');
    
    });    
    
    $app->run();
    
?>