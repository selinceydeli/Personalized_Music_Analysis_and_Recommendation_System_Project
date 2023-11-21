# Music Tailor: Music Flavor Analyzer & Recommender System

## Project Overview
This project, developed as part of CS 308's term project, aims to create an online music system. The system's core functionality is to aggregate users' liked-song data from various platforms, analyze musical preferences, and offer tailored recommendations. Unlike traditional music streaming services, this platform focuses on data collection, analysis, and recommendation, without streaming capabilities.

## Key Deliverables
1. **Data Collection & Management**
   - The platform will collect song information (track name, artist, album, user rating, etc.) through various methods:
     - Manual user input via web and mobile application
     - Batch input through file uploads (CSV, JSON, etc.)
     - Integration with cloud or self-hosted databases
     - Transfer from external music platforms (Spotify, last.fm, etc.)

2. **Analysis of Musical Choices**
   - The platform will provide statistical insights, charts, and tables about user preferences. Customizable analysis tools such as pivot tables and interactive charts will be available for advanced users.

3. **Music Recommendations**
   - Based on user ratings, listening habits, and possibly friend activities. The system will offer recommendations at song/album/artist levels, and incorporate temporal and genre-based suggestions.

4. **User Interaction Features**
   - The platform will provide:
     - Friend functionalities, including selective sharing of activity for recommendation purposes
     - Social media integration for sharing analysis results
     - Data export features for user ratings and preferences

## Technologies Used
### Backend & Web
- **Laravel**: A robust PHP framework for backend development, facilitating model-view-controller (MVC) architectural pattern, easy database migrations, and efficient routing.
- **Laravel Livewire**: A full-stack framework integrated with Laravel to create dynamic interfaces. It simplifies the management of complex UI components and state management, enhancing the user experience on the web platform.

### Mobile
- **Swift**: A powerful and intuitive programming language for iOS app development. Swift's modern syntax and safety features provide an efficient way to build high-performance, user-friendly mobile applications.

### Database
- **MySQL Server**: Our platform uses a MySQL server to manage real-time interactions with the database. MySQL's reliability and scalability make it ideal for handling complex queries and large datasets. It supports our diverse data collection methods, ensuring seamless integration of user input, batch uploads, and third-party data transfers. With MySQL, we can efficiently store, retrieve, and manipulate song information, user preferences, and analytical data, supporting the robust functionality of our music analysis and recommendation system.

## Implementation Strategy
Our implementation will focus on delivering the MVP functionalities as outlined, with an emphasis on creating a scalable and user-friendly platform. Post-MVP, we will explore extending the system's capabilities, possibly including advanced analysis tools, broader social media integration, and enhanced recommendation algorithms.

5. **Collaborators*

### Team Leader
Selin Ceydeli

### Backend Team
Onur Sezen
Oktay Çelik
Selin Ceydeli

### Web App Team
Canberk Tahıl
Ebrar Berenay Yiğit

### Mobile App Team
Ozan Çelebi
Şimal Yücel
