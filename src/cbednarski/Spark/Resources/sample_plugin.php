<?php
$spark->addPlugins('SamplePluggy',function() use ($spark) {
        $spark->testParam = "test!";
    }
);
