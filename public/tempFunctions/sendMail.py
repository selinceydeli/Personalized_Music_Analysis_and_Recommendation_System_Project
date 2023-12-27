import smtplib
import ssl
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
import argparse
parser = argparse.ArgumentParser()

# Add an argument for the input
parser.add_argument('email', type=str, help='Please enter your email')

# Parse the command-line arguments
args = parser.parse_args()


smtp_server = "smtp.gmail.com"
port = 587  # For starttls
sender_email = "musictailoredforyou@gmail.com"
password = "fmsvodnhgbgazqdg"
receiver_email = args.email
context = ssl.create_default_context()
message = MIMEMultipart("alternative")
message["Subject"] = "Music Tailor | Welcome Aboard!"
message["From"] = sender_email
message["To"] = receiver_email
html = """
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Colorful Email Template</title>
        <style>
            body {
                font-family: "Arial", sans-serif;
                margin: 0;
                padding: 0;
            }

            .container {
                max-width: 600px;
                margin: 0 auto;
                overflow: hidden; /* Clearfix */
            }

            .top-half {
                background-color: #ff4d6f;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
                padding: 20px;
                height: 70%;
                box-sizing: border-box;
            }

            .bottom-half {
                background-color: #ffffff;
                border-bottom-left-radius: 10px;
                border-bottom-right-radius: 10px;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                height: 30%;
                box-sizing: border-box;
            }

            h1,
            p {
                color: #333333;
            }

            .button {
                display: inline-block;
                padding: 10px 20px;
                margin-top: 20px;
                background-color: #ff4d6f;
                color: #ffffff;
                text-decoration: none;
                border-radius: 5px;
            }

            .footer {
                margin-top: 20px;
                text-align: center;
                color: #777777;
            }

            .logo {
                display: block;
                margin: 0 auto;
                max-width: 100%;
                height: auto;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="top-half">
                <!-- Logo -->
                <img
                    src="https://i.ibb.co/GPrLQN4/circ-logo.jpg"
                    alt="Music Tailor Logo"
                    class="logo"
                />
            </div>
            <div class="bottom-half">
                <!-- Content for the bottom half with #ffffff background -->
                <h1>Welcome to Music Tailor</h1>
                <p>
                    We are looking forward to find you songs that fits like a
                    glove
                </p>
                <p>Enjoy responsibly</p>
                <a href="http://localhost:8000" class="button">Jump Back</a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2023 Music Tailor. All rights reserved.</p>
        </div>
    </body>
</html>
"""

part = MIMEText(html, "html")
message.attach(part)
try:
    server = smtplib.SMTP(smtp_server, port)
    server.ehlo()  # Can be omitted
    server.starttls(context=context)  # Secure the connection
    server.ehlo()  # Can be omitted
    server.login(sender_email, password)
    server.sendmail(sender_email, receiver_email, message.as_bytes())

except Exception as e:
    print("Error")
else:
    print("Success")
finally:
    server.quit()
