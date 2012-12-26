chlorius [![Build Status](https://secure.travis-ci.org/chlorius/chlorius.png?branch=master)](http://travis-ci.org/chlorius/chlorius)
========

Une gallery photo simple d'utilisation.

# Status du projet

En cours de développement...

# Installation

    git clone https://github.com/chlorius/chlorius.git
    cd chlorius
    # config
    cp app/config/parameters.yml.dist app/config/parameters.yml
    # composer
    curl -s http://getcomposer.org/installer | php
    php composer.phar install
    # database
    php app/console doctrine:database:create
    php app/console doctrine:schema:create
    php app/console doctrine:database:create --env test
    php app/console doctrine:schema:create --env test

# Technique

Ce site est basé sur symfony-standard.

Il est basé sur les projets suivants :

* [Imagine][1] + [Bundle][2] + [Docs][3]
* [Bootstrap][4]
* [Bootswatch][5] - [Theme Cerulean][6]
* [Less][7] - css preprocessor
* [Gamma Gallery][8] from Codrops by Mary Lou
* [Slicebox][9] from Codrops by Pedro Botelho
* [Fine uploader][10]

[1]: https://github.com/avalanche123/Imagine
[2]: https://github.com/avalanche123/AvalancheImagineBundle
[3]: http://imagine.readthedocs.org/en/latest/
[4]: http://twitter.github.com/bootstrap/
[5]: http://bootswatch.com/
[6]: http://bootswatch.com/cerulean/
[7]: http://lesscss.org/
[8]: http://tympanus.net/codrops/2012/11/06/gamma-gallery-a-responsive-image-gallery-experiment/
[9]: http://tympanus.net/codrops/2012/10/22/slicebox-revised/
[10]: http://fineuploader.com/
