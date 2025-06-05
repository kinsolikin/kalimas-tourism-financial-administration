<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>403 Forbidden</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .wave {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 180px;
      background: url('https://svgshare.com/i/12iH.svg') repeat-x;
      background-size: cover;
      z-index: 1;
    }
  </style>
</head>
<body class="relative flex items-center justify-center min-h-screen bg-gradient-to-b from-purple-600 to-indigo-900 text-white overflow-hidden">
  <div class="text-center z-10 px-4">
    <h2 class="text-lg uppercase tracking-widest mb-2">Shoooosh!</h2>
    <h1 class="text-[100px] font-extrabold drop-shadow-[0_0_20px_rgba(255,255,255,0.8)]">403</h1>
    <h3 class="text-2xl mt-4 font-semibold">Access Denied</h3>
    <p class="mt-2 text-sm md:text-base">
      Sorry, it seems you are not permitted to see this.<br>
      If you think this is a mistake, please refer to your admin.
    </p>
    {{-- <a href="/" class="inline-block mt-6 px-6 py-3 bg-purple-400 hover:bg-purple-500 text-white font-semibold rounded-md transition">
      Go Back to Main
    </a> --}}
  </div>
  <div class="wave"></div>
</body>
</html>
