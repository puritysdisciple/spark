<?php
$spark->addPlugin('SamplePluggy',function() use ($spark) {
        $spark->testParam = "test!";
    }
);
