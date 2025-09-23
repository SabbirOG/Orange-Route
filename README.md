# 🚌 OrangeRoute

A modern university shuttle tracking and management system with real-time GPS tracking, live communication, and role-based dashboards.

## Features

- **Real-time Tracking** - Live GPS location updates with interactive maps
- **Multi-role System** - Students, drivers, and administrators with tailored dashboards
- **Live Communication** - Real-time chat between students and drivers
- **Route Management** - Admin-controlled shuttle route assignments
- **Profile Management** - User profiles with picture uploads
- **Responsive Design** - Works seamlessly on desktop and mobile devices

## Tech Stack

- **Backend**: PHP 7.4+, MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Maps**: Leaflet.js for interactive mapping
- **Server**: XAMPP (Apache + MySQL + PHP)

## Quick Start

1. **Setup XAMPP**
   ```bash
   # Download and install XAMPP
   # Start Apache and MySQL services
   ```

2. **Database Setup**
   ```sql
   # Create database 'orangeroute'
   # Import database/schema.sql
   ```

3. **Deploy**
   ```bash
   # Place project in XAMPP htdocs folder
   # Access: http://localhost/OrangeRoute/frontend/
   ```

## User Roles

| Role | Access |
|------|--------|
| **Student** | Track shuttles, chat with drivers, manage profile |
| **Driver** | Manage assigned shuttles, update location, communicate |
| **Admin** | User management, route assignments, system monitoring |

## Default Credentials

- **Admin**: `admin@orangeroute.com` / `admin123`
- **Driver**: `driver@orangeroute.com` / `driver123`
- **Student**: `student@orangeroute.com` / `student123`

## Configuration

Update database settings in `backend/db.php`:
```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "orangeroute";
```

## Security

- Password hashing with `password_hash()`
- SQL injection prevention with prepared statements
- XSS protection with `htmlspecialchars()`
- Session management and CSRF protection

## License

MIT License - Developed for university welfare purposes.

---

**Developed by [Sabbir Ahmed](https://github.com/sabbirOG)** - Making university transportation smarter and more connected! 🚌✨

## Recent Updates (September 2025)

### v2.1.0 - Enhanced User Experience
- Improved real-time tracking accuracy
- Enhanced mobile responsiveness
- Performance optimizations
- Better error handling

### v2.0.0 - Major Features
- Real-time GPS tracking
- Live chat system
- Advanced route optimization
- Multi-role dashboard system