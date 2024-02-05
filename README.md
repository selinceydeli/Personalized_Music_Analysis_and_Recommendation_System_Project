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

### Entity-Relationship Diagram Depicting Database Schema

#### Key entities in our database:
- User: Contains information about users of the system. Each user has a unique email, username, password, date of birth, and possibly subscription information. Users are able to rate songs, albums, and performers, as indicated by the 'rates' relationships.
- Song: Represents individual songs, with attributes like song ID, name, key, tempo, and language. Songs are linked to performers and albums and have attributes related to the system's music analysis, such as mood, genre, and recording type. The system entry date indicates when the song was added to the system.
- Performer: Details about the performers (artists) are stored here, including their names and nationalities. A performer can have associations with multiple songs.
- Album: Holds album-related data, including a unique album ID, name, and image URL. Albums consist of multiple songs and are rated by users.
- Rating: This associative entity captures the many-to-many relationships between users and songs/albums/performers, including the specific rating and date it was given by a user.

#### This schema forms the backbone of the data storage mechanism and enables the complex functionalities required for music analysis and recommendations. 

<img width="640" alt="Screenshot 2023-12-05 at 10 44 50 AM" src="https://github.com/selinceydeli/Personalized_Music_Analysis_and_Recommendation_System_Project/assets/120125253/817d14dc-c12e-42a8-b034-10a80ebd6590">

### Data Collection
The backend team has adeptly developed a robust and modular approach for data collection in our music analysis and recommendation system. This process is meticulously designed to harness the power of external APIs, specifically Spotify, to gather comprehensive music data. By obtaining Spotify links directly from users, the system initiates a Python script, seamlessly integrated with our PHP-based Laravel framework. This integration not only exemplifies the system's modularity but also its compatibility with diverse technologies. The script efficiently handles the retrieval, processing, and storage of music-related data, ensuring a seamless and rich user experience. This well-orchestrated process underscores our commitment to leveraging advanced technology to provide a sophisticated and user-friendly music discovery platform.

#### 1. Establishing Database Connection:
- The Python script starts by connecting to a MySQL database using mysql.connector.
- It provides the host, user, password, and database name for the connection.

#### 2. Spotify API Integration:
- The script uses Spotify's API to fetch data about music. It includes functions to get an access token (getToken) and to retrieve album data (getAlbum).

#### 3. Parsing Spotify Links:
- The parseSpotifyLink function takes a Spotify track URL, parses it, and extracts the album ID from the track information.

#### 4. Fetching and Processing Data:
- The getAlbum function fetches detailed information about an album from Spotify, including its tracks and related artists.
- It cleans and formats the data by removing unnecessary fields and adjusting data types where necessary (e.g., changing explicit flags to binary, converting musical key numbers to note names).
- The script handles JSON data for albums, songs, and artists, saving them to local files.

#### 5. Inserting Data into the Database:
- After processing, the script inserts data into the MySQL database. It includes SQL INSERT statements for performers, albums, and songs.
- Each insert operation is wrapped in a 'try-except' block to handle potential exceptions and ensure smooth database transactions.

#### 6. Command Line Interface (CLI):
- The script uses 'argparse' to create a CLI, allowing users to input a Spotify link as an argument. This makes the script easily integrable with other backend systems (like your PHP code).

#### 7. Token Management and Security:
- The script includes client credentials for the Spotify API. In a production environment, it's crucial to manage these credentials securely, often using environment variables or a secure vault.

#### 8. Closing Database Connection:
- Finally, the script closes the database cursor and connection, ensuring there are no memory leaks or unclosed connections.

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

## Mobile Team Deliverables

### Login Page Overview
The login page of our Web application provides a user-friendly and intuitive interface and handles user authentication.

<img width="350" alt="Screenshot 2024-02-05 at 3 10 45 PM" src="https://github.com/selinceydeli/Personalized_Music_Analysis_and_Recommendation_System_Project/assets/120125253/0f0c4163-1c36-4fef-99ec-b6136766ac45">

### Music Upload Page Overview
The Music Upload functionality within the mobile app allows users to easily add new tracks to their personal music collection using Spotify links. Users navigate to the 'Upload Music' section, where they are greeted with a clear, concise interface prompting them to enter the link to a Spotify song. Once the link is submitted, the app leverages the Spotify API to fetch the track's data, including the song title, artist, album, and cover art, which are then automatically integrated into the user's profile.

<img width="350" alt="Screenshot 2024-02-05 at 3 07 36 PM" src="https://github.com/selinceydeli/Personalized_Music_Analysis_and_Recommendation_System_Project/assets/120125253/786ba2b9-49b4-46ab-ba75-518df32a43c1">

### User's Profile Page Overview
The User Profile page in the mobile app serves as a personalized dashboard for users to manage their Music Tailor account. It displays the user's essential information, including their profile picture, username, email, date of birth, preferred language, and subscription type. Users have the ability to update their profile details, as reflected in the editable fields for email, date of birth, and language. The page also indicates the user's rate limit, which is a unique feature that shows how many songs they can rate within a certain period.

<img width="350" alt="Screenshot 2024-02-05 at 3 20 16 PM" src="https://github.com/selinceydeli/Personalized_Music_Analysis_and_Recommendation_System_Project/assets/120125253/81d02945-503c-43df-b11c-752d45c5d2d2">

### Recommendations & Analysis
The 'Your Music' section of the mobile app features a 'Recommendations & Analysis' module, providing users with personalized music suggestions and insights tailored to their individual tastes. The app analyzes users' listening habits, including genre preferences and song ratings, to offer custom recommendations like 'Your Genre Taste' and 'Energy & Dance Vibes'. For analytical insights, users can view their 'Daily Average Ratings', 'Top Albums by Era', 'Favorite Songs', and 'Compare Artist by Ratings'. These features combine to create a comprehensive and customized music discovery experience, ensuring that every user's unique preferences are catered to and reflected in their daily listening choices.

<img width="350" alt="Screenshot 2024-02-05 at 3 20 48 PM" src="https://github.com/selinceydeli/Personalized_Music_Analysis_and_Recommendation_System_Project/assets/120125253/f0f62a8b-c82d-4847-b9cd-9b6fcd969aba">

### Child Mode Option under Settings Page
The 'Child Mode' toggle in the settings view allows users to filter out explicit content, ensuring that the songs displayed on their profile are appropriate for all ages.

<p float="left">
  <img src="https://github.com/selinceydeli/Personalized_Music_Analysis_and_Recommendation_System_Project/assets/120125253/6e0bb25f-9ab6-4615-b48b-974f99305b67" width="350" />
  <img src="https://github.com/selinceydeli/Personalized_Music_Analysis_and_Recommendation_System_Project/assets/120125253/88499cb4-d71b-4e63-850c-9997425a3bf6" width="420" /> 
</p>

### Theme Change Option under Settings Page
The 'Change Theme' feature allows users to customize the app's appearance by selecting from various color options, which dynamically alters the color scheme of the entire application for a personalized user experience.

<img width="350" alt="Screenshot 2024-02-05 at 3 23 18 PM" src="https://github.com/selinceydeli/Personalized_Music_Analysis_and_Recommendation_System_Project/assets/120125253/79c29e0c-9a1a-49a4-aa42-02969f7ae1ed">


