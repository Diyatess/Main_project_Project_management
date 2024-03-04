import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

class LoginScreen extends StatefulWidget {
  @override
  _LoginScreenState createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final TextEditingController _emailController = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Login'),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            TextField(
              controller: _emailController,
              decoration: InputDecoration(
                labelText: 'Email',
              ),
            ),
            SizedBox(height: 16.0),
            ElevatedButton(
              onPressed: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => AttendanceMarkingScreen(email: _emailController.text.trim())),
                );
              },
              child: Text('Go to Mark Attendance Page'),
            ),
          ],
        ),
      ),
    );
  }
}

class AttendanceMarkingScreen extends StatelessWidget {
  final String email;

  const AttendanceMarkingScreen({Key? key, required this.email}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Mark Attendance'),
      ),
      body: Center(
        child: ElevatedButton(
          onPressed: () async {
            final apiUrl = 'http://localhost/Taskmaster/mark_attendance.php';

            try {
              final response = await http.post(
                Uri.parse(apiUrl),
                body: {
                  'email': email,
                },
              );

              if (response.statusCode == 200) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text('Attendance marked successfully!'),
                    duration: Duration(seconds: 3),
                  ),
                );
              } else {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text('Error marking attendance. Please try again.'),
                    duration: Duration(seconds: 3),
                  ),
                );
              }
            } catch (e) {
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                  content: Text('An error occurred. Please try again later.'),
                  duration: Duration(seconds: 3),
                ),
              );
            }
          },
          child: Text('Mark Attendance'),
        ),
      ),
    );
  }
}
