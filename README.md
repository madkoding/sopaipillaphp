SopaipillaPHP Framework
===================
[![License](https://poser.pugx.org/leaphly/cart-bundle/license.svg)](https://packagist.org/packages/leaphly/cart-bundle)
![Build test](https://travis-ci.org/SupernovaFramework/SupernovaPHP.svg?branch=master)
[![Code Climate](https://codeclimate.com/github/SupernovaFramework/SupernovaPHP/badges/gpa.svg)](https://codeclimate.com/github/SupernovaFramework/SupernovaPHP)
[![Test Coverage](https://codeclimate.com/github/SupernovaFramework/SupernovaPHP/badges/coverage.svg)](https://codeclimate.com/github/SupernovaFramework/SupernovaPHP/coverage)


Caracteristicas:
* Facil integración
* Curva rápida de aprendizaje
* Documentación actualizada
* Plugins externos

Filosofías:
* KISS (Keep it Simple and Stupid)
* DRY (Don't repeat yourself)


----------

Ejemplo de uso
==============
Coloca en la barra de dirección el modelo que quieres revisar y autogenerar
(ej: www.dominio.com/categorias)

Esto te permitirá utilizar el scaffolding integrado del framework,
éste hará ingenieria inversa de la tabla de tu base de datos con el nombre de tu modelo y
traerá los generara el CRUD automágicamente (en el caso del ejemplo, autogenerará
el controlador, el modelo, y el formulario para el modelo de categorias)

Importante:
* Asegurate de tener el modulo **rewrite** activado
* Las tablas en la base de datos se llaman en plural
* Los modelos en singular
* Los campos en singular

----------
Configuración rewrite para nginx:

location / {
    rewrite ^/$ /Public/ break;
    rewrite ^(.*)$ /Public/$1 break;
}
