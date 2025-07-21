<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>404 Page Not Found</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: #42bff5;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      color: white;
      text-align: center;
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    h1 {
      font-size: 8rem;
    }

    p {
      font-size: 2rem;
      margin: 20px 0;
    }

    .back-button {
      display: inline-block;
      padding: 10px 20px;
      background-color: white;
      color: #42bff5;
      border: 2px solid white;
      border-radius: 30px;
      text-decoration: none;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .back-button:hover {
      background-color: transparent;
      color: white;
    }

    .wave {
      position: absolute;
      bottom: 0;
      width: 100%;
    }
  </style>
</head>
<body>
  <h1>404</h1>
  <p>Oops. Page Not Found</p>
 

  <!-- Optional SVG wave -->
  <svg class="wave" viewBox="0 0 1440 320"><path fill="#ffffff" fill-opacity="1" d="M0,160L60,176C120,192,240,224,360,213.3C480,203,600,149,720,160C840,171,960,245,1080,261.3C1200,277,1320,235,1380,213.3L1440,192L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"></path></svg>
</body>
</html>
