<!-- carpeta raiz/gen_hash.php -->
 
<?php
$plain = 'Hostinger1980#'; // <-- CAMBIA por la contraseña que quieras usar dentro de las comillas simples
$hash = password_hash($plain, PASSWORD_DEFAULT);
echo "Hash: " . $hash;

// utilizar en http://localhost/maleja/gen_hash.php

// Su funcionamiento se debe documentar en el README.md del proyecto, indicando que se debe ejecutar este script para generar el hash de la contraseña de la base de datos antes de iniciar el proyecto.
// Asegúrate de que el archivo gen_hash.php no quede en producción, es solo para generar el hash de la contraseña.