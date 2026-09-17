# Manual de Instalación y Despliegue
## Mesa de Partes Virtual - IESP Túpac Amaru Cusco

Este documento contiene los pasos detallados para la instalación, configuración y puesta en marcha del sistema tanto en entornos de desarrollo local (XAMPP en Windows) como en servidores de producción (Linux / Apache).

---

### 1. Requisitos del Servidor
- **PHP:** Versión 8.2.0 o superior (compatible con PHP 8.3).
- **Extensiones PHP requeridas:**
  - `pdo` y `pdo_mysql` (acceso a base de datos relacional)
  - `mbstring` (manipulación segura de caracteres UTF-8)
  - `fileinfo` (detección precisa de tipos MIME reales)
  - `openssl` (seguridad criptográfica y TLS para SMTP)
  - `json` (auditoría y respuestas de API)
- **Base de Datos:** MySQL 8.0+ o MariaDB 10.4+ (Collation recomendado: `utf8mb4_unicode_ci`).
- **Servidor Web:** Apache 2.4+ con módulo `mod_rewrite` habilitado.
- **Configuración recomendada de `php.ini`:**
  ```ini
  upload_max_filesize = 25M
  post_max_size = 30M
  memory_limit = 256M
  date.timezone = America/Lima
  ```

---

### 2. Instalación en Entorno Windows con XAMPP

#### Paso 2.1: Ubicación del Código
Copie la carpeta del proyecto dentro del directorio raíz de documentos de Apache de XAMPP:
```
C:\xampp\htdocs\mesa-tupac\
```

#### Paso 2.2: Configuración del Entorno (.env)
Copie el archivo `.env.example` como `.env` en la raíz del proyecto y configure los parámetros de su conexión local:
```ini
APP_NAME="Mesa de Partes Virtual - IESP Túpac Amaru"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost/mesa-tupac
APP_TIMEZONE=America/Lima

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mesa_partes_tupac
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

#### Paso 2.3: Creación e Inicialización de la Base de Datos
1. Inicie los servicios de **Apache** y **MySQL** desde el Panel de Control de XAMPP.
2. Abra una terminal en `C:\xampp\htdocs\mesa-tupac\` y ejecute la creación y poblado de datos oficiales:
   ```powershell
   mysql -u root -e "CREATE DATABASE IF NOT EXISTS mesa_partes_tupac CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -u root mesa_partes_tupac < database/schema.sql
   php database/run_seed.php
   ```
   *(Nota: `run_seed.php` garantiza la correcta inserción de tildes, caracteres en quechua y nombres institucionales en UTF-8 nativo).*

#### Paso 2.4: Permisos de Carpetas de Almacenamiento
Asegúrese de que el servidor web tenga permisos de escritura en:
- `storage/documents/` (archivos adjuntos cargados por los usuarios)
- `storage/logs/` (registro de errores y correos enviados)
- `public/assets/img/` (logotipos personalizados subidos por el administrador)
- `public/assets/css/variables.css` (personalizador de colores)

#### Paso 2.5: Verificación de Acceso
El sistema soporta acceso transparente tanto con o sin `/public/` en la URL:
- Portal Principal: `http://localhost/mesa-tupac/`
- Formulario FUT: `http://localhost/mesa-tupac/tramite`
- Consulta Pública: `http://localhost/mesa-tupac/consulta`
- Acceso a Funcionarios: `http://localhost/mesa-tupac/login`
- Diagnóstico de Salud: `http://localhost/mesa-tupac/health`

---

### 3. Instalación en Servidor de Producción (Linux / Ubuntu / Debian)

#### Paso 3.1: Instalación de Paquetes
```bash
sudo apt update
sudo apt install apache2 php8.2 php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-fileinfo mariadb-server git
sudo a2enmod rewrite
```

#### Paso 3.2: Clonación y Permisos
```bash
cd /var/www/
sudo git clone https://github.com/instituto/mesa-tupac.git
cd /var/www/mesa-tupac/
sudo chown -R www-data:www-data storage/ public/assets/img/ public/assets/css/
sudo chmod -R 775 storage/ public/assets/img/ public/assets/css/
```

#### Paso 3.3: Configuración del VirtualHost de Apache
Cree el archivo `/etc/apache2/sites-available/mesa-tupac.conf`:
```apache
<VirtualHost *:80>
    ServerName mesadepartes.tupacamaru.edu.pe
    DocumentRoot /var/www/mesa-tupac/public

    <Directory /var/www/mesa-tupac/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/mesa_tupac_error.log
    CustomLog ${APACHE_LOG_DIR}/mesa_tupac_access.log combined
</VirtualHost>
```
Habilite el sitio y reinicie Apache:
```bash
sudo a2ensite mesa-tupac.conf
sudo systemctl restart apache2
```

---

### 4. Prueba y Verificación del Sistema
Para corroborar que todos los componentes, modelos, tablas y transacciones funcionan al 100%, ejecute el script de integración oficial:
```bash
php tests/test_full_workflow.php
```
Si todas las pruebas concluyen con éxito, el sistema imprimirá en consola:
`¡TODAS LAS PRUEBAS END-TO-END PASARON CON 100% DE ÉXITO!`
