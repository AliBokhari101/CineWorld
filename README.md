🎬 Cinema Ticketing Management System

An elegant, full-featured **web-based cinema ticket booking system** built using **PHP and MySQL**. This project enables users to browse movies, view showtimes, select seats, and book tickets with a modern, responsive UI. Inspired by the Netflix-style layout, it enhances the cinema booking experience for users while providing smooth administrative control in the backend.

---
🔧 Technologies Used

- Frontend: HTML5, CSS3, JavaScript  
- Styling Libraries: Google Fonts, Animate.css  
- Backend: PHP  
- Database: MySQL (phpMyAdmin)  
- Tools: XAMPP / WAMP, VS Code

---

✨ Key Features

🧑‍💼 User Functionality
- ✅ User Login & Registration
- 🎞️ Movie Browsing
  - “Now Showing” and “Upcoming Movies” sections
  - Trailers and screen type indicators
- 📅 Schedule Selection
  - Dynamically loaded from the `screenings` table
- 💺 Interactive Seat Selection
  - Color-coded:  
    - 🟡 Premium — 600 PKR  
    - 💗 VIP — 900 PKR  
    - 🔴 Booked — Locked  
    - ⚪ Standard — 500 PKR  
- 🧾 Booking Summary
  - Movie info, date, time, selected seats, seat-wise pricing & total
- 💳 Payment Upload
  - Upload payment proof (screenshot)
  - Status: `verified` / `unverified`

---

👨‍💻 Admin Functionality
- 🎬 Add, Edit, Delete Movies
- 🕒 Manage Screening Schedules
- 📊 Access Bookings and Payment Status

---

📁 Project Structure

```
/Cinema Ticketing System
├── index.php              # Homepage
├── booking.php            # Showtime & seat selection
├── process_booking.php    # Booking summary and bill
├── payment.php            # Payment upload interface
├── view_movies.php        # Admin: movie list & controls
├── config/
│   └── db.php             # DB connection
├── css/
│   ├── booking.css
│   ├── process_booking.css
│   └── ...
└── assets/
    └── images/posters/
```

---

🗃️ Database Schema Overview

### `users`
| Column     | Type         |
|------------|--------------|
| id         | INT (PK)     |
| name       | VARCHAR      |
| email      | VARCHAR      |
| password   | VARCHAR (hashed) |

`movies`
| Column       | Type         |
|--------------|--------------|
| id           | INT (PK)     |
| title        | VARCHAR      |
| duration     | VARCHAR      |
| language     | VARCHAR      |
| screen_type  | VARCHAR      |
| poster_url   | TEXT         |

`screenings`
| Column         | Type         |
|----------------|--------------|
| id             | INT (PK)     |
| movie_id       | INT (FK)     |
| screening_day  | DATE         |
| screening_time | TIME         |

`bookings`
| Column          | Type         |
|-----------------|--------------|
| id              | INT (PK)     |
| user_id         | INT (FK)     |
| movie_id        | INT (FK)     |
| screening_day   | DATE         |
| screening_time  | TIME         |
| selected_seats  | TEXT         |
| total_price     | INT          |
| payment_proof   | TEXT         |
| status          | VARCHAR      |

---

🧪 Setup Instructions

1. Clone this repository or download the ZIP
2. Import the SQL database via phpMyAdmin
3. Set up the project under `htdocs` (XAMPP) or `www` (WAMP)
4. Update DB credentials in `config/db.php`
5. Launch the app at:  
   `http://localhost/Cinema%20Ticketing%20System/index.php`

---

📌 Future Improvements

- Admin dashboard with graphical stats
- Email booking confirmations
- Payment gateway integration

---
📬 Contact

For queries or collaboration: Ali Bokhari
Email: alibokhari4903@hotmail.com
GitHub: AliBokhari101

---
