<<<<<<< HEAD
# Orange-Route
OrangeRoute is a smart shuttle tracking and management system for students. It features role-based dashboards for users, drivers, and admins with secure authentication, route assignments, and real-time shuttle updates. Built using PHP, MySQL, HTML, CSS, and JavaScript.
=======
# 🚌 OrangeRoute - University Shuttle Tracking System

A comprehensive shuttle tracking and communication system designed for university welfare, built with modern web technologies and a beautiful orange & white minimalistic theme.

## ✨ Features

### For Students
- **Real-time Shuttle Tracking** - View active shuttles and their current status
- **Live Communication** - Chat with drivers and other students
- **Profile Management** - Update profile information and pictures
- **Status Notifications** - Get updates on shuttle delays and traffic

### For Drivers
- **Shuttle Management** - Start/stop routes and report traffic conditions
- **Passenger Communication** - Chat with students and provide updates
- **Route Assignment** - Receive route assignments from administrators
- **Status Updates** - Report real-time shuttle status

### For Administrators
- **User Management** - Manage students, drivers, and system users
- **Route Assignment** - Assign shuttle routes to drivers
- **System Overview** - Monitor system health and user activity
- **Dashboard Analytics** - View user statistics and engagement

## 🎨 Design

- **Orange & White Theme** - Modern, minimalistic design with university-friendly colors
- **Responsive Layout** - Works perfectly on desktop, tablet, and mobile devices
- **Smooth Animations** - Engaging user experience with CSS transitions
- **Intuitive Navigation** - Easy-to-use interface for all user types

## 🛠️ Technologies Used

- **Backend**: PHP 7.4+, MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Database**: MySQL with InnoDB engine
- **Server**: XAMPP (Apache + MySQL + PHP)
- **Styling**: Custom CSS with CSS Variables and Flexbox/Grid

## 📋 Prerequisites

- XAMPP (Apache + MySQL + PHP)
- Web browser (Chrome, Firefox, Safari, Edge)
- Text editor (VS Code, Sublime Text, etc.)

## 🚀 Installation

1. **Clone the Repository**
   ```bash
   git clone <repository-url>
   cd OrangeRoute
   ```

2. **Set up XAMPP**
   - Download and install XAMPP
   - Start Apache and MySQL services
   - Place the project in `htdocs` folder

3. **Database Setup**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `orangeroute`
   - Import the schema from `database/schema.sql`

4. **Configure Database Connection**
   - Update `backend/db.php` if needed (default settings work with XAMPP)

5. **Set Permissions**
   - Ensure `uploads/profile_pictures/` directory is writable
   - Set proper file permissions for uploads

6. **Access the Application**
   - Open browser and go to `http://localhost/OrangeRoute/frontend/`

## 👥 User Roles

### Student (User)
- Email: `@bscse.uiu.ac.bd` or similar university email
- Can track shuttles, chat with drivers, manage profile

### Driver
- Email: `@driver.uiu.bd`
- Can manage assigned shuttles, communicate with students
- Can start/stop routes and report traffic

### Administrator
- Special admin role (manually assigned in database)
- Can manage all users and assign routes
- Full system access and monitoring

## 📱 Usage Guide

### For Students
1. **Sign Up** - Create account with university email
2. **Login** - Access your dashboard
3. **Track Shuttles** - View active shuttles and their status
4. **Chat** - Communicate with drivers and other students
5. **Profile** - Update your information and profile picture

### For Drivers
1. **Sign Up** - Create account with driver email
2. **Wait for Assignment** - Admin will assign you a route
3. **Manage Shuttle** - Start/stop your assigned route
4. **Report Status** - Update traffic conditions and delays
5. **Communicate** - Chat with students and other drivers

### For Administrators
1. **Access Admin Panel** - Login with admin credentials
2. **Manage Users** - View all registered users
3. **Assign Routes** - Assign shuttle routes to drivers
4. **Monitor System** - Check system health and activity

## 🔧 Configuration

### Database Configuration
Edit `backend/db.php` to match your database settings:
```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "orangeroute";
```

### File Upload Settings
Ensure upload directory has proper permissions:
```bash
chmod 755 uploads/profile_pictures/
```

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check XAMPP MySQL service is running
   - Verify database credentials in `db.php`
   - Ensure database `orangeroute` exists

2. **File Upload Issues**
   - Check directory permissions
   - Verify upload directory exists
   - Check PHP upload settings

3. **Session Issues**
   - Clear browser cookies
   - Check PHP session configuration
   - Restart Apache service

4. **Chat Not Working**
   - Check JavaScript console for errors
   - Verify chat.php is accessible
   - Check database table `general_chat` exists

## 📊 Database Schema

The system uses the following main tables:
- `users` - User accounts and profiles
- `shuttles` - Shuttle information and status
- `general_chat` - Chat messages
- `email_verifications` - Email verification codes
- `password_resets` - Password reset tokens

## 🔒 Security Features

- Password hashing with PHP's `password_hash()`
- SQL injection prevention with prepared statements
- Session management and authentication
- File upload validation
- XSS protection with `htmlspecialchars()`

## 🚀 Future Enhancements

- Real-time GPS tracking integration
- Push notifications for mobile devices
- Advanced analytics and reporting
- Mobile app development
- Integration with university systems

## 📝 License

This project is developed for university welfare purposes. All rights reserved.

## 👨‍💻 Developer

**Developed by Sabbir Ahmed** - A project created to help the student community with better transportation tracking and communication.

## 🤝 Contributing

This is a community project aimed at helping students. For suggestions or improvements, please contact Sabbir Ahmed.

---

**OrangeRoute** - Making university transportation smarter and more connected! 🚌✨
>>>>>>> fixes/full-review
