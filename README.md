Link demo: https://drive.google.com/file/d/14xvuJGWA3BtsZk6SzxyFf-6yTXknqQVZ/view?usp=sharing
# Seijaku Studio Pilates

A web-based pilates studio management system with booking functionality, video tutorials, and achievement tracking.

## Features

- **Member Portal**: Book classes, view bookings, watch video tutorials, track achievements
- **Instructor Panel**: Manage class schedules, view students
- **Admin Dashboard**: Manage bookings, classes, instructors, messages, and videos
- **Authentication**: Login/Register with role-based access (admin, instructor, member)
- **Responsive Design**: Works on desktop and mobile devices

## Project Structure

```
seijaku-pilates/
├── api/                    # REST API endpoints
│   ├── auth/              # Authentication (login, register)
│   ├── bookings/          # Booking management
│   ├── classes/           # Class schedules and types
│   ├── instructors/       # Instructor management
│   ├── messages/          # Contact messages
│   ├── notifications/     # User notifications
│   ├── reviews/           # User reviews/testimonials
│   ├── users/             # User management
│   └── videos/            # Video tutorials
├── classes/               # PHP class files (OOP)
├── config/                # Database configuration
├── pages/                 # Dashboard pages
│   ├── admin/            # Admin dashboard pages
│   ├── instructor/       # Instructor panel pages
│   └── member/           # Member portal pages
└── public/               # Public assets
    ├── css/              # Stylesheets
    ├── images/           # Images and avatars
    ├── js/               # JavaScript files
    └── index.php         # Main entry point
```

## Requirements

- PHP 8.0 or higher
- MySQL database

## Setup

1. Configure database connection in `config/database.php`
2. Set up the database schema (tables for users, bookings, classes, etc.)
3. Start PHP development server:
   ```bash
   cd seijaku-pilates && php -S 0.0.0.0:5000 -t public
   ```
4. Access the application at `http://localhost:5000`

## User Roles

- **Admin**: Full access to manage all aspects of the studio
- **Instructor**: Can view their class schedules and students
- **Member**: Can book classes, view tutorials, and track achievements

## Technology Stack

- **Backend**: PHP 8.x
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Styling**: Custom CSS with responsive design
- **Icons**: Font Awesome 6.x
- **Fonts**: Google Fonts (Poppins)
