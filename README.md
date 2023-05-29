# Prueba Técnica para WebFactura

Creado Por Matías Soto Leiva

## Requisitos

Asegúrate de tener los siguientes requisitos instalados en tu entorno de desarrollo:

- Symfony 5.4
- PHP 7.4
- MySQL (motor de base de datos)
- Bootstrap 5 (para el frontend)
- Asegúrate de tener Node.js y Yarn instalados en tu entorno de desarrollo:
  El proyecto utiliza Webpack Encore para la gestión de assets y compilación de recursos front-end. .
- Recomiendo utilizar Laragon para un entorno de desarrollo, ya que es bastante sencillo de agregar otras versiones y PHP.


### Archivos CSV para probar
En la raíz de la carpeta, existe dos archivos ``csv`` para ser utilizados para probar la importación:
``error.csv`` & ``galería.csv``
## Instalación

1. Clona el repositorio en tu máquina local
2. Navega hasta el directorio del proyecto
3. Instala las dependencias del proyecto utilizando Composer:  
    ``composer install ``
4. Configura la conexión a la base de datos en el archivo `.env`:
  ``DATABASE_URL=mysql://usuario:contraseña@localhost/nombre_base_de_datos``
5. Crea la base de datos y ejecuta las migraciones:  
   ``php bin/console doctrine:database:create``  
   ``php bin/console doctrine:migrations:migrate``
6. Ejecuta el siguiente comando para instalar las dependencias de Node.js:  
    ``yarn install``
7. Una vez completada la instalación, ejecuta el siguiente comando para compilar los assets y observar los cambios 
en tiempo real:  
    ``yarn encore dev --watch``
8. Inicia el servidor local de Symfony:  
  ``symfony serve``
