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

## Collaborators

### Team Leader
- Selin Ceydeli

### Backend Team
- Onur Sezen
- Oktay Çelik
- Selin Ceydeli

### Web App Team
- Canberk Tahıl
- Ebrar Berenay Yiğit

### Mobile App Team
- Ozan Çelebi
- Şimal Yücel

## Backend Team Deliverables


## Web Team Deliverables

### Main Page Overview
The main page of our Web application provides a user-friendly and intuitive interface, showcasing the core features of our music analysis and recommendation system. It's designed to be visually appealing and easy to navigate, ensuring a seamless user experience.
<img width="1440" alt="Screenshot 2023-12-05 at 10 38 30 AM" src="https://github.com/selinceydeli/Personalized_Music_Analysis_and_Recommendation_System_Project/assets/120125253/871b9d81-52ff-4a3d-a4d8-e1a91ff196b1">

### Search Music Functionality
Our music search functionality allows users to easily find songs, artists, and albums. Users can input keywords, and our system performs a comprehensive search across various metadata fields to deliver accurate results.
<img width="1440" alt="Screenshot 2023-12-05 at 10 24 28 AM" src="https://github.com/selinceydeli/Personalized_Music_Analysis_and_Recommendation_System_Project/assets/120125253/40d87730-840a-4a47-a1d3-6948691dab9e">

### Music Upload Functionality
Users can enrich our music database by uploading songs via Spotify links. This feature not only adds the song to our database but also automatically extracts and stores relevant data like the artist and album, creating dedicated pages for each.
<img width="1440" alt="Screenshot 2023-12-05 at 10 23 41 AM" src="https://github.com/selinceydeli/Personalized_Music_Analysis_and_Recommendation_System_Project/assets/120125253/eed52279-afa0-4517-9136-8f0d8cfeb464">

### Personalized Music Recommendations
Our system offers personalized song recommendations based on a user's specific taste in genres and preferences for energy and danceability. Recommendations are presented as interactive music cards, linking directly to detailed album pages.
<img width="1440" alt="Screenshot 2023-12-05 at 10 23 34 AM" src="https://github.com/selinceydeli/Personalized_Music_Analysis_and_Recommendation_System_Project/assets/120125253/566d581e-baff-44cf-9f46-a13d60810500">




