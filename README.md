# Image processing and optimization toolkit for Laravel

This package provides image processing using [Imagine](https://github.com/php-imagine/Imagine)
and optimization using [Spatie Image Optimizer](https://github.com/spatie/image-optimizer).
[Libvips](https://github.com/libvips/libvips) support for Imagine is available through [Imagine-Vips](https://github.com/rokka-io/imagine-vips) and
basic SVG manipulation is available through [Imagine SVG](https://github.com/contao/imagine-svg).

Raster image processing can use different packages such as
**imagick**, **gmagick**, **vips** and **gd** as backend.

**Imagick** is a good choice if you need the full feature set of
[Imagine](https://github.com/php-imagine/Imagine). **Vips** is
faster and uses less memory, but requires a separate PHP extension
and is missing some more advanced features.

For optimization, additional libraries have to be installed at your
system. See installation chapter.

## Installation

You can install the package via composer:

```bash
composer require mehr-it/levi-images
```

### Testing your installation
This library depends on various system requirements for all 
features to work. You can use following command to check
the installed components:

```bash
php ./artisan levi-images:test
```

### Installing optimization tools

The optimizer uses various optimization tools as they are
available at your system:

- [JpegOptim](http://freecode.com/projects/jpegoptim) 
- [Optipng](http://optipng.sourceforge.net/)
- [Pngquant 2](https://pngquant.org/)
- [SVGO 1](https://github.com/svg/svgo)
- [Gifsicle](http://www.lcdf.org/gifsicle/)
- [cwebp](https://developers.google.com/speed/webp/docs/precompiled)

You can install them on Debian/Ubuntu using following commands:

```bash
sudo apt-get install jpegoptim
sudo apt-get install optipng
sudo apt-get install pngquant
sudo npm install -g svgo@1.3.2
sudo apt-get install gifsicle
sudo apt-get install webp
```

For more details see [Spatie Image Optimizer](https://github.com/spatie/image-optimizer).


### Installing libvips driver

[Libvips](https://github.com/libvips/libvips) is a fast, multi-threaded image processing
library which needs far less memory than eg. Imagick.

Version 8.7 or higher of libvips is highly recommended. `paste` and `rotate` by angles other than multipliers of 90 are not supported with older versions of libvips.

**Regardless of the libvips version, not all Imagine features are supported**. See [Imagine-Vips](https://github.com/rokka-io/imagine-vips) for details.

You also need the [php-vips-ext](https://github.com/libvips/php-vips-ext) extension version 1.0.8 or higher. You can install libvips driver requirements as follows:

1. [Install the libvips library and headers](https://libvips.github.io/libvips/install.html). It's in the linux package managers, homebrew and MacPorts, and there are Windows binaries on the vips website. For example, on Debian:

   ```bash
   sudo apt-get install libvips-dev
   ```

2. Install the binary PHP extension. You'll need a PHP development environment for this, since it will download and build the sources for the extension. For example, on Debian:

   ```bash
   sudo apt-get install php-pear
   ```

   Then to download and build the extension:

   ```bash
   pecl install vips
   ```

   You may need to add `extension=vips.so` or equivalent to `php.ini`, see the output of pecl.


3. Install [Imagine-Vips](https://github.com/rokka-io/imagine-vips):
   
   ```bash
   composer require rokka/imagine-vips
   ```


## Usage

This package has three image processing sections.

1. **Raster** Provides an API to load or create raster images
   for manipulation or conversion to other formats.


2. **Vector** Provides an API to load vector images for 
   some basic manipulation.


3. **Optimization** provides an API to optimize images in their 
   given format. Usually optimizers reduce the image file size 
   and optimize the loading process, eg. interlaced loading.


You can access all APIs using the `LeviImages` facade, eg. 
`LeviImages::raster()`.


### Raster images
The raster image API can be accessed as follows:

```php
LeviImages::raster()
```

It offers three different methods to open images:
      
```php
// open a file 
$im = LeviImages::raster()->open('path/to/file.png');

// load a string 
$im = LeviImages::raster()->load(/* image data as string */);

// read from a resource
$im = LeviImages::raster()->load(fopen('path/to/file.png', 'r'));    
```

All these methods return an instance of `Imagine\Image\ImageInterface`
for [Imagine](https://github.com/php-imagine/Imagine) image manipulations.

#### Configuring the raster image driver

The concrete implementation depends on the backends available at
your system and your driver selection. By default a driver is
chosen automatically. **Imagick** is preferred, if multiple 
drivers are available. However, if you prefer another driver such
as **Vips** you can set another default driver.

Use `php ./artisan vendor:publish` to publish the package configuration
and set the desired driver:

```php
// levi-images.php
return [
     'raster' => [
         /*
          * Configures the raster image driver to use. Available options are "vips", "imagick", "gmagick" and "gd". If
          * "auto" is set, the first available driver is chosen.
          */
         'driver' => 'vips',
     ],
];    
   ```

#### Creating new raster images
To create a new raster image from the scratch, simply call
`create()` with the desired size and background color:

```php
$size    = new Box(100, 100);
$bgColor = (new RGB())->color("ff0000", 1); // red

$im = LeviImages::raster()->create($size, $bgColor);  
```

#### Importing (vector) images
Occasionally you might want to use [Imagine](https://github.com/php-imagine/Imagine)
instances created by another library with your current driver or
**import vector images** to merge them with raster images. You
can easily do so by calling `import()`. The following example
demonstrates how to merge a vector image with a raster image:

```php
$rasterImage = LeviImages::raster()->open('path/to/file.png');
$vectorImage = LeviImages::vector()->open('path/to/file.svg');

$imported = LeviImages::raster()->import($vectorImage); 

$rasterImage->paste($imported, new Point(0, 0)) 
```

**NOTE:** The `"gd"` driver does not support loading vector images.

This returns a new image instance which can be used with the 
current driver.

Internally the imported image is exported in a compatible format
and loaded utilizing the current driver.


### Vector images

The vector image API can be accessed as follows:

```php
LeviImages::vector()
```

It offers the same methods to open images as the raster API.

```php
// open a file 
$im = LeviImages::raster()->open('path/to/file.svg');

// load a string 
$im = LeviImages::raster()->load(/* image data as string */);

// read from a resource
$im = LeviImages::raster()->load(fopen('path/to/file.svg', 'r'));    
```

All these methods return an instance of `Contao\ImagineSvg\Image`.
Event though [Imagine SVG](https://github.com/contao/imagine-svg) implements the
`Imagine\Image\ImageInterface`, it only offers a limited set of
manipulations and some behave a little different from raster
images. However, some basic operations, such as resizing are 
available and very useful.

The vector image API does not offer methods for creating new
images or importing from other instances.


### Optimization

The optimization API can be accessed as follows:

```php
LeviImages::optimizer()
```

The optimizer utilizes different optimization libraries which
are installed at your system and applies them to the files
which should be optimized. If no optimization library for a 
given file type is available, the file is silently kept as it
is.

#### Optimizing image files

You can optimize image files using `optimizeFile()`. **If you
do not want to modify the original file**, a separate output
path can be given as second parameter:

```php
// optimize file in place
LeviImages::optimizer()
    ->optimizeFile('path/to/file.png');

// create a new optimized image file
LeviImages::optimizer()
    ->optimizeFile('path/to/file.png', 'path/to/output.png');
```

#### Optimizing resources

If image data is available as resource, `optimizeResource()`
processes the image and returns a new resource with optimized
image data:

```php
$res = LeviImages::optimizer()
    ->optimizeResource(fopen('path/to/file.png', 'r'));
```

#### Passing image instances

If working with raster or vector image objects, you can directly pass
the image object to `optimizeImage()`. However, you have to
specify the desired output format as second parameter. You will
receive a resource with the optimized image data:

```php
$res = LeviImages::optimizer()
    ->optimizeImage($img, Optimizer::FORMAT_PNG);
```

**Note:** The only supported output format for vector images
is `Optimizer::FORMAT_SVG`. If you want a raster image output,
first import as raster image.


#### Customizing optimizers

If you want to use a custom set of optimizers or other than default
optimization settings, you can set a resolver function which 
returns the optimizers to use:

```php
LeviImages::optimizer()
    ->setOptimizersResolver(function() {
    
        return [
            new Jpegoptim([
                '--max=85',
                '--strip-all',
                '--all-progressive',
            ]),
            return new Cwebp([
                '-m 6',
                '-pass 10',
                '-mt',
                '-q 80',
            ]); 
        ];    
    });
```

Using `setOptimizersResolver()` changes the global optimizer chain.
If you only want to change optimizers for a single optimization,
you can use `useOptimizers()` to create a new instance with the
given optimizers:

```php
LeviImages::optimizer()
    ->useOptimizers([
        new Jpegoptim([
                '--max=90',
            ]),
       ]
   );
```

All given optimizers must implement the `\Spatie\ImageOptimizer\Optimizer`
interface. See [Spatie Image Optimizer](https://github.com/spatie/image-optimizer) 
for details.


## License

This package is released unter MIT license.

[Imagine SVG](https://github.com/contao/imagine-svg) is released under LGPL-3.0-or-later.
