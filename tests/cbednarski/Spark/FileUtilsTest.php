<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Spark\FileUtils;

class FileUtilsTest extends PHPUnit_Framework_TestCase
{
    public function testMkdirIfNotExists()
    {
        $path = __DIR__ . '/magicpath';
        $this->assertFileNotExists($path);

        FileUtils::mkdirIfNotExists($path);
        $this->assertFileExists($path);
        $this->assertTrue(is_dir($path));

        rmdir($path);
        $this->assertFileNotExists($path);
    }

    public function testDirIsEmpty()
    {
        $this->assertFalse(FileUtils::dirIsEmpty(__DIR__));

        $path = __DIR__ . '/magicpath';
        FileUtils::mkdirIfNotExists($path);
        $this->assertTrue(FileUtils::dirIsEmpty($path));

        rmdir($path);
        $this->assertFileNotExists($path);
        $this->assertFalse(FileUtils::dirIsEmpty($path));
    }

    public function testExistsAndIsReadable()
    {
        $file = __DIR__ . '/magicfile';
        $this->assertFalse(FileUtils::existsAndIsReadable($file));

        touch($file);
        $this->assertTrue(FileUtils::existsAndIsReadable($file));

        chmod($file, 000);
        $this->assertFalse(FileUtils::existsAndIsReadable($file));

        chmod($file, 700);
        unlink($file);
        $this->assertFileNotExists($file);
    }

    public function testListFilesInDir()
    {
        $path = realpath(__DIR__ . '/../../assets/');
        $files = FileUtils::listFilesInDir($path);

        # Make this test deterministic
        sort($files);

        $expected = array(
            $path . '/sample_render.html',
            $path . '/spark-deploy.yml',
            $path . '/spark.yml',
            $path . '/spark_custom.yml',
            $path . '/spark_ignore.yml',
            $path . '/spark_partial.yml',
            $path . '/subfolder/some_file.txt',
        );

        $this->assertEquals($expected, $files);
    }

    public function testListFileInMissingDir()
    {
        $path = __DIR__ . '/asdlkfjasdklfjhdsfoiruewr';
        $this->assertFalse(file_exists($path));

        $files = FileUtils::listFilesInDir($path);
        $this->assertEquals(array(), $files);
    }

    public function testMkdirs()
    {
        $path = __DIR__ . '/magicpath/';
        $dirs = array('pie', 'cake', 'icecream');

        FileUtils::mkdirs($dirs, $path);
        $this->assertTrue(is_dir($path));

        foreach ($dirs as $dir) {
            $this->assertTrue(is_dir($path . $dir));
            rmdir($path . $dir);
            $this->assertFalse(is_dir($path . $dir));
        }

        rmdir($path);
        $this->assertFalse(is_dir($path));
    }

    public function testConcat()
    {
        file_put_contents('magicfile1', 'some stuff');
        FileUtils::concat('magicfile2', 'magicfile1');
        $this->assertEquals('some stuff', file_get_contents('magicfile2'));

        FileUtils::concat('magicfile1', 'magicfile2');
        $expected = 'some stuff' . PHP_EOL . 'some stuff';
        $this->assertEquals($expected, file_get_contents('magicfile1'));

        unlink('magicfile1');
        unlink('magicfile2');
    }

    public function testRecursiveDelete()
    {
        FileUtils::mkdirIfNotExists(__DIR__ . '/magicdelete/blah/blee/bloo');

        $file_path = __DIR__ . '/magicdelete/blah/blee/thisisafile';
        touch($file_path);
        $this->assertFileExists($file_path);

        $return = FileUtils::recursiveDelete(__DIR__ . '/magicdelete');
        $this->assertFileNotExists(__DIR__ . '/magicdelete');
        $this->assertEquals(1, $return);
    }

    public function testRecursiveDeleteMissing()
    {
        $path = __DIR__ . '/asdlkfjasdklfjhdsfoiruewr';
        $this->assertFileNotExists($path);

        $return = FileUtils::recursiveDelete($path);
        $this->assertEquals(0, $return);
    }

    public function testPathDiff()
    {
        $path = FileUtils::pathDiff('/path/to/blah', '/path/to/blah/and/some/more');
        $this->assertEquals('/and/some/more', $path);

        $path = FileUtils::pathDiff('/path/to/blah', '/path/to/blah/and/some/more', true);
        $this->assertEquals('and/some/more', $path);

        $path = FileUtils::pathDiff('/path/to/blah', '/path/to/cake');
        $this->assertEquals('', $path);
    }

    public function testFilterExists()
    {
        $actual = FileUtils::filterExists(array('thisfiledoesntexist', 'northisone'));
        $this->assertEquals(array(), $actual);

        $path = realpath(__DIR__ . '/../../assets');

        $expected = array(
            $path . '/sample_render.html',
            $path . '/spark.yml'
        );

        $actual = FileUtils::filterExists(
            array(
                $path . '/sample_render.html',
                $path . '/magicfalskdjfds',
                $path . '/spark.yml',
                $path . '/filedoesnotexist.blah',
            )
        );

        $this->assertEquals($expected, $actual);
    }

    public function testRemoveTwigExtension()
    {
        $this->assertEquals('index.html', FileUtils::removeTwigExtension('index.twig'));
        $this->assertEquals('index.html', FileUtils::removeTwigExtension('index.html.twig'));
        $this->assertEquals('twig.html', FileUtils::removeTwigExtension('twig.twig'));
        $this->assertEquals('twig/blah.html', FileUtils::removeTwigExtension('twig/blah.twig'));
        $this->assertEquals('twig/twig.html', FileUtils::removeTwigExtension('twig/twig.html.twig'));
        $this->assertEquals('twig/twig.html', FileUtils::removeTwigExtension('twig/twig.html.twig'));
        $this->assertEquals('.twig.css', FileUtils::removeTwigExtension('.twig.css.twig'));
    }

    public function testMatchFilename()
    {
        $filename = 'some/file/you-want-to-match.md';

        $match1 = 'some/file/you-want';
        $this->assertTrue(FileUtils::matchFilename($filename, $match1), 'Basic regex / string match');

        $matches1 = array($match1);
        $this->assertTrue(FileUtils::matchFilename($filename, $matches1), 'Match using an array');

        $matches2 = array('some/file/you-want$');
        $this->assertFalse(FileUtils::matchFilename($filename, $matches2), 'Array match that should fail');

        $this->assertFalse(FileUtils::matchFilename($filename, array()), 'Array match with empty array should fail');
        $this->assertFalse(FileUtils::matchFilename($filename, null), 'Match with null should fail');
        $this->assertTrue(FileUtils::matchFilename($filename, ''), 'Match with empty string should catch everything');
    }

    public function testFileIsHidden()
    {
        $this->assertTrue(\cbednarski\Spark\FileUtils::fileIsHidden('.'));
        $this->assertTrue(\cbednarski\Spark\FileUtils::fileIsHidden('.woot.php'));
        $this->assertTrue(\cbednarski\Spark\FileUtils::fileIsHidden('/root/.idea/woot.php'));
        $this->assertFalse(\cbednarski\Spark\FileUtils::fileIsHidden('root/hello.php'));
        $this->assertFalse(\cbednarski\Spark\FileUtils::fileIsHidden('hello.php'));
    }

    public function testSoftRealpath()
    {
        $this->assertEquals("/user/some/path", FileUtils::softRealpath("/user/blah-_0938!#$%&'()+,;=@[]^`{}~/../some/path"));
        $this->assertEquals("/user/blah-_0938!#$%&'()+,;=@[]^`{}~/some/path", FileUtils::softRealpath("/user/blah-_0938!#$%&'()+,;=@[]^`{}~/./some/path"));
        $this->assertEquals("/user/blah-_0938!#$%&'()+,;=@[]^`{}~/some/path", FileUtils::softRealpath("/user/blah-_0938!#$%&'()+,;=@[]^`{}~//some/path"));
        $this->assertEquals("user/some/path", FileUtils::softRealpath("user/blah-_0938!#$%&'()+,;=@[]^`{}~/../some/path"));
        $this->assertEquals("user/blah-_0938!#$%&'()+,;=@[]^`{}~/some/path", FileUtils::softRealpath("user/blah-_0938!#$%&'()+,;=@[]^`{}~/./some/path"));
        $this->assertEquals("user/blah-_0938!#$%&'()+,;=@[]^`{}~/some/path", FileUtils::softRealpath("user/blah-_0938!#$%&'()+,;=@[]^`{}~//some/path"));
    }
}
