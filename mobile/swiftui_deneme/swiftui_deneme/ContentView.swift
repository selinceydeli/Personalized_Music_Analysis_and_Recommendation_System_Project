import SwiftUI

struct ContentView: View {
    @State private var username = ""
    @State private var password = ""
    @State private var wrongUsername = 0
    @State private var wrongPassword = 0
    @State private var showingLoginScreen = false
    
    var body: some View {
        NavigationView{
            ZStack{
                Color.pink
                    .ignoresSafeArea()
                Circle()
                    .scale(1.7)
                    .foregroundColor(.white.opacity(0.15))
                Circle()
                    .scale(1.35)
                    .foregroundColor(.white)
                VStack{
                    Text("Welcome Back To")
                        .font(.largeTitle)
                        .bold()
                    Text("Music Tailor")
                        .font(Font.system(size: 36, design: .rounded))
                        .bold()
                        .foregroundColor(.pink)

                    TextField("Username", text: $username)
                        .padding()
                        .frame(width: 300, height: 50)
                        .background(Color.black.opacity(0.05))
                        .cornerRadius(10)
                        .border(.red, width: CGFloat(wrongUsername))
                    
                    
                    SecureField("Password", text: $password)
                        .padding()
                        .frame(width: 300, height: 50)
                        .background(Color.black.opacity(0.05))
                        .cornerRadius(10)
                        .border(.red, width: CGFloat(wrongPassword))
                    Spacer().frame(height: 20)

                    Button(action: {
                        authenticateUser(username: username, password: password)
                    }) {
                        Text("Login")
                            .foregroundColor(.white)
                            .frame(width: 300, height: 50)
                            .background(Color.pink)
                            .cornerRadius(10)
                    }
                    Button(action: {
                        authenticateUser(username: username, password: password)
                    }) {
                        Text("Forgot your password?")
                            .foregroundColor(.black)
                            .frame(width: 300, height: 50, alignment: .leading)
                            
                    }

                    
                    NavigationLink(destination: Text("You are logged in \(username)!"), isActive: $showingLoginScreen){
                        EmptyView()
                    }
                }
    
            }
            .navigationBarHidden(true)
        }
    }
    
    func authenticateUser(username: String, password: String) {
        if username.lowercased() == "ozan"{
            wrongUsername = 0
            if password.lowercased() == "1234" {
                wrongPassword = 0
                showingLoginScreen = true
            }
            else{
                wrongPassword = 2
            }
        }
        else {
            wrongUsername = 2
        }
    }
}

#Preview {
    ContentView()
}
