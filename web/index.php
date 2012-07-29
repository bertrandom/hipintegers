<?php

    require_once __DIR__.'/../vendor/autoload.php';

    $app = new Silex\Application();

    $app['debug'] = true;

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/views',
    ));

    $app->get('/integer/mission/get/', function () use ($app) {
        
        $browser = new Buzz\Browser();
        $response = $browser->submit('http://www.missionintegers.com/next-int', array('format' => 'json'));
        $body = $response->getContent();
        $data = json_decode($body);
        
        echo $data->number;
        
        return 'fuck';
        
    });

    $app->get('/create', function () use ($app) {
        
        $browser = new Buzz\Browser();
        $response = $browser->submit('http://www.missionintegers.com/next-int', array('format' => 'json'));
        $body = $response->getContent();
        $data = json_decode($body);

        $mission_integer = $data->number;

        $browser = new Buzz\Browser();
        $response = $browser->submit('http://api.brooklynintegers.com/rest/', array('method' => 'brooklyn.integers.create'));
        $body = $response->getContent();
        $data = json_decode($body);

        $brooklyn_integer = $data->integer;

        $hipster_integer = $mission_integer + $brooklyn_integer;

        // print ("<pre>");
        // echo $hipster_integer;
        // print ("</pre>");
        
        header('Content-Type: image/png');

        // Create the image
        $im = imagecreatetruecolor(612, 612);

        // Create some colors
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 611, 611, $white);

        // The text to draw
        $text = 'Testing...';
        // Replace path by your own font path
        $font = '/../fonts/Neue Haas Grotesk Display W02 Medium.ttf';

        // Add the text
        $bbox = imagettfbbox(72, 0, $font, $hipster_integer);
        imagettftext($im, 72, 0, floor((611 - $bbox[4] - $bbox[0]) / 2), floor((611 - $bbox[5] - $bbox[1]) / 2), $black, $font, $hipster_integer);

        // Using imagepng() results in clearer text compared with imagejpeg()
        imagepng($im);
        imagedestroy($im);        
        
        
    });

    $app->get('/', function () use ($app) {
        
        return $app['twig']->render('index.twig.html', array(
            'name' => $name,
        ));
        
    });
    
    $app->run();
    
?>