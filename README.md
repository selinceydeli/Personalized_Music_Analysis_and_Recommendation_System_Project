<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

Music Tailor: Music Flavor Analyzer & Recommender System

## Project Overview
This project, developed as part of CS 308's term project, aims to create an online music system. The system's core functionality is to aggregate users' liked-song data from various platforms, analyze musical preferences, and offer tailored recommendations. Unlike traditional music streaming services, this platform focuses on data collection, analysis, and recommendation, without streaming capabilities.

## Key Deliverables
1. Data Collection & Management
The platform will collect song information (track name, artist, album, user rating, etc.) through various methods:
Manual user input via web and mobile application
Batch input through file uploads (CSV, JSON, etc.)
Integration with cloud or self-hosted databases
Transfer from external music platforms (Spotify, last.fm, etc.)

2. Analysis of Musical Choices
The platform will provide statistical insights, charts, and tables about user preferences. Customizable analysis tools such as pivot tables and interactive charts will be available for advanced users.

3. Music Recommendations
Based on user ratings, listening habits, and possibly friend activities. The system will offer recommendations at song/album/artist levels, and incorporate temporal and genre-based suggestions.

4. User Interaction Features
The platform will provide:
Friend functionalities, including selective sharing of activity for recommendation purposes
Social media integration for sharing analysis results
Data export features for user ratings and preferences

## Technologies Used
Backend & Web
Laravel: A robust PHP framework for backend development, facilitating model-view-controller (MVC) architectural pattern, easy database migrations, and efficient routing.
Laravel Livewire: A full-stack framework integrated with Laravel to create dynamic interfaces. It simplifies the management of complex UI components and state management, enhancing the user experience on the web platform.
Mobile
Swift: A powerful and intuitive programming language for iOS app development. Swift's modern syntax and safety features provide an efficient way to build high-performance, user-friendly mobile applications.
Database
MySQL Server: Our platform uses a MySQL server to manage real-time interactions with the database. MySQL's reliability and scalability make it ideal for handling complex queries and large datasets. It supports our diverse data collection methods, ensuring seamless integration of user input, batch uploads, and third-party data transfers. With MySQL, we can efficiently store, retrieve, and manipulate song information, user preferences, and analytical data, supporting the robust functionality of our music analysis and recommendation system.

Implementation Strategy
Our implementation will focus on delivering the MVP functionalities as outlined, with an emphasis on creating a scalable and user-friendly platform. Post-MVP, we will explore extending the system's capabilities, possibly including advanced analysis tools, broader social media integration, and enhanced recommendation algorithms.


## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
