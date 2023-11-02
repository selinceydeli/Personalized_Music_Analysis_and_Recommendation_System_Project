import SwiftUI

struct SignUpView: View {
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
                        Text("Get started with")
                            .font(.largeTitle)
                            .bold()
                            .foregroundColor(.black)
                            .frame(width: 300, height: 5, alignment: .leading)
                        Text("Music Tailor")
                            .font(Font.system(size: 36, design: .rounded))
                            .bold()
                            .foregroundColor(.pink)
                            .frame(width: 300, height: 50, alignment: .leading)
                        
                    
                    

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
                        //func
                    }) {
                        Text("Sign Up")
                            .foregroundColor(.white)
                            .frame(width: 300, height: 50)
                            .background(Color.pink)
                            .cornerRadius(10)
                    }
                    HStack {
                        Text("Already have an account?")
                            .foregroundColor(.black)
                        NavigationLink(destination: LoginView()) {
                            Text("Login")
                                .foregroundColor(.pink)
                                .bold()
                        }
                        .navigationBarBackButtonHidden(true) // Hide the back button
                        Spacer()
                    }
                    .padding(.horizontal, 50)
                    .padding(.vertical, 5)
                    
                    
                    
                    
                    NavigationLink(destination: Text("You are logged in \(username)!"), isActive: $showingLoginScreen){
                        EmptyView()
                    }
                }
    
            }
        }
    }
}

#Preview {
    SignUpView()
}
