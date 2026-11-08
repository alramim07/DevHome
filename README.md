# DevHome - Developer Portfolio Platform

<div align="center">

![DevHome Logo](https://img.shields.io/badge/DevHome-Portfolio%20Platform-blue?style=for-the-badge)
![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4.0-06B6D4?style=for-the-badge&logo=tailwind-css)
![Tests](https://img.shields.io/badge/Tests-153%20Passing-brightgreen?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**A modern, feature-rich portfolio platform built with Laravel 12 for developers to showcase their projects, manage CVs, and build their online presence.**

[🚀 Live Website](https://devhome.onrender.com) • [📖 Documentation](#documentation)

</div>

## 📋 Table of Contents

- [🌟 Features](#-features)
- [🛠️ Technology Stack](#️-technology-stack)
- [📁 Project Structure](#-project-structure)
- [🗄️ Database Schema](#️-database-schema)
- [🚀 Installation](#-installation)
- [⚙️ Configuration](#️-configuration)
- [🔧 Development](#-development)
- [📝 API Documentation](#-api-documentation)
- [🚀 Deployment](#-deployment)
- [📄 License](#-license)

---

## 🌟 Features

### 👥 Public-Facing Features
- **🎨 Modern Portfolio Display** - Responsive showcase of developer projects with organized layouts
- **🌓 Solar & Lunar Eclipse Theme Switcher** - Animated dark/light toggle featuring high-contrast solar corona and crescent moon eclipse transitions
- **🔍 Advanced Project Filtering** - Filter projects by technology stack (Laravel, PHP, MERN, Themes)
- **📱 Mobile-First Design** - Fully responsive with seamless theme adaptation
- **⚡ AJAX-Powered Navigation** - Dynamic loading with smooth transitions
- **📊 Project Analytics** - Track project views with IP-based deduplication
- **📧 Contact Form** - Professional contact system with validation
- **📰 Published Blog Articles** - Public articles reader (`/blog/{slug}`) with suggested posts
- **👥 Team Display** - Showcase team members and their profiles (`/team/{slug}`)
- **🗺️ Dynamic Sitemap & Robots.txt** - Auto-generated Google-compliant `/sitemap.xml` and `/robots.txt`
- **🔗 SEO-Friendly URLs** - Clean URLs with slugs and meta tags
- **📄 Rich Project Content** - HTML content rendering with rich text editor
- **👤 Developer Profile Pages** - Individual profile pages for each developer (`/team/{slug}`)
- **📥 CV Download** - Generate and download professional CVs in PDF format
- **📬 Project Contact Form** - Direct contact form on project pages
- **🗂️ Organized View Structure** - Modular frontend layouts for better maintainability

### 🏆 Key Technical Highlights
- **🏗️ Service-Based Architecture** - Modular services (`DashboardService`, `HomeService`, `ProjectService`) for clean separation of concerns
- **🔐 Custom RBAC System** - Built from scratch without packages: Position presets with permission inheritance, per-user overrides, Super Admin hierarchy with mutual protection
- **🗄️ Advanced Database Design** - Soft deletes, JSON columns for flexible tech stacks/languages, polymorphic relationships ready
- **⚡ AJAX-First Frontend** - Alpine.js + Axios for seamless SPA-like experience without SPA complexity
- **🎨 Self-Hosted Rich Text Editor** - TinyMCE 5 (GPL) with custom image upload to Supabase S3, no external CDN dependency
- **📄 PDF Generation Pipeline** - Browsershot + Puppeteer + Spatie PDF for server-side CV generation
- **🔍 SEO-Ready** - Dynamic sitemap.xml, robots.txt, meta tags, semantic HTML, clean slugs
- **🧪 Comprehensive Testing** - 153 tests (556 assertions) covering RBAC, CRUD, sitemap, security, edge cases
- **🐳 Production-Ready** - Docker-ready, Render/Heroku compatible, Supabase S3 storage, health checks
- **🔒 Security-First** - CSRF protection, rate limiting, mass assignment guards, XSS prevention, SQL injection prevention

### 🛡️ Admin Panel Features
- **🔐 Granular Role-Based Access Control (RBAC)** - Position presets, customizable per-section permissions, and Super Admin hierarchy
- **👥 Team Management Suite** - Comprehensive management for Positions (`/positions`), Teams (`/teams`), and All Members (`/members`)
- **📊 Enhanced Analytics Dashboard** - Real-time statistics with modular components
- **📁 Project Management** - Full CRUD operations with rich text editor
- **📰 Blog Publishing Engine** - Create, edit, draft, and publish articles with slug handling (`/add-blog`, `/blog-list`)
- **👤 Profile Management** - Update personal information, social links, and custom CV
- **📸 Image Uploads** - Handle project thumbnails, profile images, and in-editor images
- **📧 Email Tracking** - Monitor and manage contact submissions
- **⚙️ Settings Management** - Dynamic website configuration
- **📝 Advanced To-Do System** - Single-date & date-range filtering, newest/oldest date sorting, and bulk deletion
- **📞 Contact Management** - View and manage website contacts
- **📄 CV Management** - Upload custom CV PDFs and generate DevHome CVs
- **🔧 Dashboard Service** - Modular service-based architecture

---

## 🛠️ Technology Stack

### Backend Technologies
| Technology | Version | Purpose |
|------------|---------|---------|
| **Laravel** | 12.0 | PHP Framework |
| **PHP** | 8.2+ | Backend Language |
| **MySQL** | 5.7+ | Database |
| **Propaganistas Laravel Phone** | 6.0 | Phone Validation |
| **Spatie Laravel PDF** | 2.2 | PDF Generation |
| **Spatie Browsershot** | 5.2 | Screenshot/PDF from HTML |

### Frontend Technologies
| Technology | Version | Purpose |
|------------|---------|---------|
| **TailwindCSS** | 4.0 | CSS Framework |
| **Alpine.js** | Latest | JavaScript Interactivity |
| **CKEditor 5** | 47.5 | Rich Text Editor |
| **Vite** | 7.0 | Build Tool |
| **Axios** | 1.11.0 | HTTP Client |
| **Lucide** | 0.575 | Icon Library |
| **Puppeteer** | 24.37 | Headless Chrome for PDF |

### Development Tools
| Tool | Purpose |
|------|---------|
| **Laravel Tinker** | Console Debugging |
| **PHPUnit** | Testing Framework |
| **Laravel Pint** | Code Formatting |
| **Concurrently** | Multi-Process Development |
| **Laravel Sail** | Database Schema Visualization |
| **Laravel Pail** | Error Logging |

---

## 📁 Project Structure

```
DevHome/
├── 📂 app/
│   ├── 📂 Http/
│   │   ├── 📂 Controllers/
│   │   │   ├── 📂 Admin/
│   │   │   │   ├── 📄 AdminController.php
│   │   │   │   ├── 📄 DevController.php
│   │   │   │   ├── 📄 EmailController.php
│   │   │   │   ├── 📄 ProfileController.php
│   │   │   │   ├── 📄 ProjectController.php
│   │   │   │   ├── 📄 TodoController.php
│   │   │   │   └── 📄 WebsiteSettingsController.php
│   │   │   ├── 📄 CVController.php
│   │   │   ├── 📄 HomeController.php
│   │   │   └── 📄 TeamController.php
│   │   └── 📂 Requests/
│   │       ├── 📄 AddDevRequest.php
│   │       ├── 📄 ContactRequest.php
│   │       ├── 📄 ProfileUpdateRequest.php
│   │       ├── 📄 ProjectContactRequest.php
│   │       ├── 📄 ProjectRequest.php
│   │       └── 📄 WebsiteSettingsRequest.php
│   ├── 📂 Models/
│   │   ├── 📄 User.php
│   │   ├── 📄 Projects.php
│   │   ├── 📄 ProjectView.php
│   │   ├── 📄 Contact.php
│   │   ├── 📄 Email.php
│   │   ├── 📄 Todo.php
│   │   ├── 📄 TechStack.php
│   │   └── 📄 Website_settings.php
│   └── 📂 Services/
│       ├── 📄 DashboardService.php
│       ├── 📄 HomeService.php
│       └── 📄 ProjectService.php
├── 📂 config/
├── 📂 database/
│   ├── 📂 migrations/
│   ├── 📂 factories/
│   └── 📂 seeders/
├── 📂 public/
│   ├── 📂 admin/ (Admin Panel Assets)
│   ├── 📂 front/ (Frontend Assets)
│   └── 📂 storage/ (Uploads)
├── 📂 resources/
│   ├── 📂 views/
│   │   ├── 📂 admin/
│   │   │   ├── 📂 dashboard/
│   │   │   │   ├── 📄 email-list.blade.php
│   │   │   │   ├── 📄 overview.blade.php
│   │   │   │   ├── 📄 recent_projects.blade.php
│   │   │   │   └── 📄 todo.blade.php
│   │   │   ├── 📂 layouts/
│   │   │   ├── 📂 projects/
│   │   │   │   ├── 📄 add.blade.php
│   │   │   │   ├── 📄 edit.blade.php
│   │   │   │   └── 📄 list.blade.php
│   │   │   ├── 📄 403_redirect.blade.php
│   │   │   ├── 📄 add-dev.blade.php
│   │   │   ├── 📄 contacts.blade.php
│   │   │   ├── 📄 dashboard.blade.php
│   │   │   ├── 📄 dev_list.blade.php
│   │   │   ├── 📄 email_list.blade.php
│   │   │   ├── 📄 login.blade.php
│   │   │   ├── 📄 profile.blade.php
│   │   │   ├── 📄 todo.blade.php
│   │   │   └── 📄 website_settings.blade.php
│   │   ├── 📂 front/
│   │   │   ├── 📂 home/
│   │   │   │   ├── 📄 about.blade.php
│   │   │   │   ├── 📄 hero.blade.php
│   │   │   │   ├── 📄 index.blade.php
│   │   │   │   ├── 📄 portfolio.blade.php
│   │   │   │   ├── 📄 project.blade.php
│   │   │   │   ├── 📄 project-contact.blade.php
│   │   │   │   ├── 📄 stats.blade.php
│   │   │   │   └── 📄 team.blade.php
│   │   │   ├── 📂 layouts/
│   │   │   │   ├── 📄 contact.blade.php
│   │   │   │   ├── 📄 footer.blade.php
│   │   │   │   ├── 📄 header.blade.php
│   │   │   │   └── 📄 master.blade.php
│   │   │   ├── 📂 partials/
│   │   │   │   ├── 📄 messages.blade.php
│   │   │   │   └── 📄 project_cards.blade.php
│   │   │   └── 📂 team/
│   │   │       └── 📄 index.blade.php
│   │   └── 📂 pdf/
│   │       └── 📄 dev_cv.blade.php
│   ├── 📄 css/app.css
│   └── 📄 js/app.js
├── 📂 routes/
│   └── 📄 web.php
├── 📂 storage/
├── 📄 composer.json
├── 📄 package.json
├── 📄 phpunit.xml
└── 📄 vite.config.js
```

---

## 🗄️ Database Schema

### Core Tables

#### `users` (Extended Laravel Users)
```sql
id, name, slug (unique), email, password, email_verified_at, remember_token
profile_fields: image, custom_cv, designation, facebook_url, linkedin_url, 
                github_url, experience, languages (JSON), deleted_at
```

#### `projects`
```sql
id, user_id (FK), title, slug (unique), description (HTML), thumbnail
github_url, live_url, tech_stack (JSON), views, timestamps
```

#### `project_views` (Analytics)
```sql
id, projects_id (FK), ip_address, created_at, updated_at
```

#### `contacts` (Contact Form)
```sql
id, first_name, last_name, email, phone_number, message, 
created_at, updated_at
```

#### `website_settings` (Dynamic Configuration)
```sql
id, logo, logo_dark, tagline, tagline_h, tagline_p, about_us_img1, 
about_us_img2, what_we_build, why_dev_home, how_we_work, 
footer_des, hero_svg1-4 (longText), timestamps
```

#### `emails` (Email Management)
```sql
id, user_id (FK), email_address, subject, body, 
created_at, updated_at
```

#### `todos` (Task Management)
```sql
id, task, task_time, user_id (FK), completed, 
created_at, updated_at, deleted_at
```

#### `tech_stacks` (Technology Categories)
```sql
id, name, slug, created_at, updated_at
```

### Laravel System Tables
- `cache`, `jobs`, `sessions` (Database driver)

---

## 🚀 Installation

### 📋 Prerequisites
- **PHP 8.2+**
- **MySQL 5.7+** or **MariaDB**
- **Composer**
- **Node.js 18+** and **NPM**
- **Web Server** (Apache/Nginx with mod_rewrite)
- **Chrome/Chromium** (for PDF generation via Browsershot)

### ⚡ Quick Setup

1. **🌐 Clone the Repository**
```bash
git clone https://github.com/lynsromero/DevHome.git
cd DevHome
```

2. **🔧 Automated Setup** (Recommended)
```bash
composer run setup
```
> This command handles everything: dependencies, environment setup, migrations, and asset building.

3. **🍪 Manual Setup** (Alternative)
```bash
# Install PHP dependencies
composer install

# Copy environment configuration
cp .env.example .env

# Generate application key
php artisan key:generate

# Create database and update .env with your credentials
# Then run migrations
php artisan migrate

# Install Node.js dependencies
npm install

# Build frontend assets
npm run build
```

4. **🚀 Start Development Server**
```bash
# Development mode (runs server, queue, and vite simultaneously)
composer run dev

# Or individual services
php artisan serve          # Laravel server
npm run dev                # Vite development server
php artisan queue:listen   # Queue worker
```

### 🌐 Web Server Configuration

#### Apache
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /path/to/DevHome/public
    
    <Directory /path/to/DevHome/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/DevHome/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

---

### 🔑 Default Credentials

After seeding the database (`php artisan db:seed`), the following administrative accounts are available:

| Role | Email | Password | Access Level |
|------|-------|----------|--------------|
| **Super Admin** | `devhomeadmin@gmail.com` | `DevhOme@sp01` | Full system access (all sections & settings) |
| **Member / Demo Admin** | `john.doe@example.com` | `password` | Standard administrative sections |

---

## ⚙️ Configuration

### 📄 Environment Variables

Key `.env` settings to configure:

```bash
# Application
APP_NAME=DevHome
APP_URL=http://localhost
APP_DEBUG=true

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=developershome
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Session & Cache
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

# Mail (for production)
MAIL_MAILER=smtp
MAIL_HOST=your-mail-server
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password

# BrowserShot (for PDF generation)
BROWSERSHOT_CHROME_PATH=/usr/bin/google-chrome
```

### 🎨 Customization

#### Logo & Branding
Update through admin panel or modify `website_settings` table:
- `logo` - Light theme logo
- `logo_dark` - Dark theme logo
- `tagline` - Site tagline
- `footer_des` - Footer description

#### Colors & Styling
Edit `resources/css/app.css` and `tailwind.config.js` for custom theming.

---

## 🔧 Development

### 📦 Dependencies

#### PHP Packages (5 main)
- `laravel/framework` ^12.0 - Main framework
- `laravel/tinker` ^2.10.1 - Console tool
- `propaganistas/laravel-phone` ^6.0 - Phone validation
- `spatie/laravel-pdf` ^2.2 - PDF generation
- `spatie/browsershot` ^5.2 - HTML to PDF/screenshots

#### Node Packages
- `vite` ^7.0.7 - Build tool
- `tailwindcss` ^4.0.0 - CSS framework
- `@tailwindcss/vite` ^4.0.0 - Vite integration
- `laravel-vite-plugin` ^2.0.0 - Laravel integration
- `axios` ^1.11.0 - HTTP client
- `ckeditor5` ^47.5.0 - Rich text editor
- `lucide` ^0.575.0 - Icon library
- `puppeteer` ^24.37.5 - Headless Chrome
- `concurrently` ^9.0.1 - Multi-process runner

### 🧪 Testing

```bash
# Run all tests
composer run test
# or
php artisan test

# Run specific test
php artisan test --filter ProjectTest
```

### 📝 Code Quality

```bash
# Code formatting
vendor/bin/pint

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 🔄 Asset Building

```bash
# Development build (with hot reload)
npm run dev

# Production build
npm run build

# Watch for changes
npm run dev -- --watch
```

---

## 📝 API Documentation

### 🌐 Public Routes

| Method | URI | Purpose |
|--------|-----|---------|
| GET | `/` | Home page with projects |
| GET | `/project/{slug}` | Individual project view with HTML content |
| POST | `/contact-us` | Contact form submission |
| POST | `/contact-me` | Project contact form submission |
| GET | `/team/{slug}` | Developer profile page |
| GET | `/cv/devhome/{slug}` | Generate DevHome CV as PDF |
| GET | `/cv/custom/{slug}` | Download custom CV PDF |

### 🔐 Authentication Routes

| Method | URI | Purpose |
|--------|-----|---------|
| GET | `/login` | Login page |
| POST | `/login` | Login processing |
| GET | `/logout` | Logout |

### 🛡️ Protected Admin Routes

| Method | URI | Purpose |
|--------|-----|---------|
| GET | `/dashboard` | Admin dashboard |
| GET | `/profile` | Profile management |
| POST | `/profile-update` | Update profile with CV upload |
| GET | `/add-project` | Add project form |
| POST | `/store-project` | Save project |
| GET | `/edit-project/{slug}` | Edit project form |
| POST | `/update-project/{slug}` | Update project |
| GET | `/project-list` | List projects |
| POST | `/project-delete/{slug}` | Delete project |
| GET | `/website-settings` | Website configuration |
| POST | `/website/update` | Update settings |
| GET | `/email-list` | View contact emails |
| GET | `/website-contacts` | View website contacts |
| GET | `/add-dev` | Add developer form |
| POST | `/add-dev` | Save developer |
| GET | `/dev-list` | Manage developers |
| GET | `/remove-dev/{id}` | Remove developer |
| POST | `/remove-dev-ajax/{id}` | AJAX remove developer |
| GET | `/todo` | Task management |
| POST | `/todo/store` | Create task |
| DELETE | `/todo/delete/{id}` | Delete task |

### 🔄 AJAX Endpoints

- **Project Filtering**: `/?filter={technology}` - Returns filtered projects
- **Load More**: `/?filter={tech}&load_all=true` - Loads all projects
- **Contact Submit**: `/contact-us` - Handles contact form with validation
- **Project Contact**: `/contact-me` - Handles project-specific contact form
- **Image Upload**: `/project-image-upload` - Upload images for TinyMCE editor

---

## 🚀 Deployment

### 📦 Production Checklist

1. **🔧 Environment Setup**
```bash
APP_ENV=production
APP_DEBUG=false
```

2. **🏗️ Build Assets**
```bash
npm run build
composer install --optimize-autoloader --no-dev
```

3. **🗄️ Database Setup**
```bash
php artisan migrate --force
php artisan db:seed --force
```

4. **⚡ Optimization**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

5. **📁 File Permissions**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### 🐳 Docker Deployment (Optional)

```dockerfile
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    chromium \
    chromium-driver

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application
COPY . /var/www/html

# Install dependencies
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader

# Build frontend
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
```

---

## 🎯 Project Status

> **✅ Production Ready** - DevHome is live and fully functional at [https://devhome.onrender.com](https://devhome.onrender.com) with all core features implemented and tested.

### ✅ Completed Features (All 110 Tests Passing)
- ✅ **Portfolio System** - AJAX filtering, project showcase with tech stacks
- ✅ **Admin Panel** - Full authentication with granular RBAC (Super Admin > Admin > Member)
- ✅ **Team Management** - Positions CRUD, Teams CRUD, Members management with soft delete/restore
- ✅ **Super Admin System** - Real super admin account (devhomeadmin@gmail.com / DevhOme@sp01) with mutual protection
- ✅ **Granular RBAC** - Position presets with permission presets, per-user permission overrides
- ✅ **Team Management Suite** - Positions CRUD, Teams CRUD, Members management with filters/soft delete
- ✅ **Solar & Lunar Eclipse Theme** - Animated dark/light toggle with solar corona & crescent moon
- ✅ **Dynamic Sitemap & Robots.txt** - Auto-generated `/sitemap.xml` (Homepage + Projects + Blogs) & `/robots.txt`
- ✅ **Blog Engine** - Admin CRUD + Public hidden blog (`/blog/{slug}`) with contact form & suggested posts
- ✅ **Project CRUD** - Rich text editor (TinyMCE), tech stacks, thumbnails, GitHub/live URLs
- ✅ **AJAX Filtering** - Real-time project filtering by tech stack with load-more
- ✅ **Contact Forms** - Website contact + project-specific contact forms with AJAX
- ✅ **Analytics** - Project view tracking with IP deduplication
- ✅ **Dynamic Settings** - Website settings management via admin panel
- ✅ **Developer Profiles** - Public profiles at `/team/{slug}` with social links, skills, CV download
- ✅ **Advanced To-Do** - Date filtering, date-range, sorting, bulk delete
- ✅ **Modular Dashboard** - Service-based architecture with DashboardService
- ✅ **CV System** - Custom CV upload + DevHome CV PDF generation (Browsershot/Puppeteer)
- ✅ **Image Uploads** - Self-hosted TinyMCE with in-editor image uploads (Supabase S3)
- ✅ **Granular RBAC** - Position presets with permission presets, per-user overrides
- ✅ **Super Admin Protection** - Mutual protection (cannot delete/change role of other super admins)
- ✅ **Dynamic Sitemap** - Auto-generated `/sitemap.xml` (Homepage + Projects + Blogs only)
- ✅ **Robots.txt** - Auto-generated with admin paths disallowed
- ✅ **Security Hardening** - CSRF, rate limiting, mass assignment protection, XSS prevention

---

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

---

## 📞 Support

- 📧 **Email**: [sakibramim4@gmail.com](mailto:sakibramim4@gmail.com)
- 🐛 **Issues**: [GitHub Issues](https://github.com/lynsromero/DevHome/issues)

---

## 📖 Documentation {#documentation}

### 🔧 Quick Start
1. **Clone & Setup**
   ```bash
   git clone https://github.com/lynsromero/DevHome.git
   cd DevHome
   composer run setup
   ```

2. **Configure Environment**
   ```bash
   cp .env.example .env
   # Edit .env with your database & mail credentials
   ```

3. **Run Development Server**
   ```bash
   composer run dev
   ```

### 🔐 Default Credentials
| Role | Email | Password |
|------|-------|----------|
| **Super Admin** | `devhomeadmin@gmail.com` | `DevhOme@sp01` |
| **Demo Member** | `john.doe@example.com` | `password` |

### 🌐 Key Routes
| Purpose | URL |
|---------|-----|
| Homepage | `/` |
| Admin Login | `/login` |
| Admin Dashboard | `/dashboard` |
| Projects Admin | `/add-project`, `/project-list` |
| Blog Admin | `/add-blog`, `/blog-list` |
| Team Management | `/positions`, `/teams`, `/members` |
| Public Blog | `/blog/{slug}` |
| Team Profiles | `/team/{slug}` |
| Project View | `/project/{slug}` |
| Sitemap | `/sitemap.xml` |
| Robots.txt | `/robots.txt` |

### 🛡️ RBAC Permissions
| Section | Description |
|---------|-------------|
| `projects` | Project CRUD operations |
| `blog` | Blog CRUD operations |
| `team_management` | Positions, Teams, Members management |
| `website_settings` | Site configuration |
| `website_contacts` | Contact form submissions |
| `join_requests` | Team join requests |
| `email_list` | Email submissions |
| `todo` | Task management |

### 🛡️ RBAC Roles
| Role | Permissions |
|------|-------------|
| **Super Admin** | All sections + user/role management + super admin protection |
| **Admin** | Granted sections only |
| **Member** | Granted sections only (position preset pre-fills) |

### 🔧 Key Artisan Commands
```bash
# Testing
php artisan test                    # Run all tests (110 tests)
php artisan test --filter=SitemapTest

# Cache Management
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear

# Database
php artisan migrate --force
php artisan db:seed --force

# Assets
npm run build
npm run dev
```

### 🚀 Deployment
```bash
# Production Build
npm run build
composer install --optimize-autoloader --no-dev

# Database
php artisan migrate --force
php artisan db:seed --force

# Optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 🔧 Environment Variables
```bash
APP_NAME=DevHome
APP_URL=https://yourdomain.com
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-db
DB_USERNAME=your-user
DB_PASSWORD=your-password

MAIL_MAILER=smtp
MAIL_HOST=your-mail-server
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password

BROWSERSHOT_CHROME_PATH=/usr/bin/google-chrome
```

---

## 📞 Support

- 📧 **Email**: [sakibramim4@gmail.com](mailto:sakibramim4@gmail.com)
- 🐛 **Issues**: [GitHub Issues](https://github.com/lynsromero/DevHome/issues)

---

<div align="center">

**⭐ Star this repository if it helped you!**

[🔝 Back to top](#devhome---developer-portfolio-platform)

</div>
