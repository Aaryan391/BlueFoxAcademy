<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
	<title>404 - Page Not Found</title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
			font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
		}

		body {
			min-height: 100vh;
			background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
			display: flex;
			align-items: center;
			justify-content: center;
			color: #ffffff;
			overflow: hidden;
		}

		.container {
			text-align: center;
			padding: 2rem;
			position: relative;
			z-index: 1;
		}

		/* Standard and vendor-prefixed property definitions for compatibility */
		.error-code {
			font-size: 12rem;
			font-weight: 800;
			background: linear-gradient(45deg, #FF6B6B, #4ECDC4);

			/* Vendor-prefixed for older Chrome and Safari (e.g., Safari 3, Chrome 4-8) */
			-webkit-background-clip: text;

			/* Standard property for modern browsers */
			background-clip: text;

			/* Ensure the text fill is transparent to show the gradient */
			-webkit-text-fill-color: transparent;

			animation: pulse 2s infinite;
			margin-bottom: 1rem;
		}


		.error-title {
			font-size: 2.5rem;
			margin-bottom: 1.5rem;
			opacity: 0;
			transform: translateY(20px);
			animation: fadeInUp 0.6s ease forwards 0.3s;
		}

		.error-message {
			font-size: 1.2rem;
			color: #cccccc;
			max-width: 600px;
			margin: 0 auto 2rem;
			opacity: 0;
			transform: translateY(20px);
			animation: fadeInUp 0.6s ease forwards 0.6s;
		}

		.buttons {
			display: flex;
			gap: 1rem;
			justify-content: center;
			opacity: 0;
			transform: translateY(20px);
			animation: fadeInUp 0.6s ease forwards 0.9s;
		}

		.btn {
			padding: 1rem 2rem;
			border-radius: 50px;
			font-size: 1.1rem;
			font-weight: 500;
			text-decoration: none;
			transition: all 0.3s ease;
			position: relative;
			overflow: hidden;
		}

		.btn-primary {
			background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
			color: white;
			border: none;
		}

		.btn-secondary {
			background: transparent;
			color: white;
			border: 2px solid rgba(255, 255, 255, 0.2);
		}

		.btn:hover {
			transform: translateY(-2px);
			box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
		}

		.particles {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			pointer-events: none;
		}

		@keyframes pulse {
			0% {
				transform: scale(1);
			}

			50% {
				transform: scale(1.05);
			}

			100% {
				transform: scale(1);
			}
		}

		@keyframes fadeInUp {
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		@media (max-width: 768px) {
			.error-code {
				font-size: 8rem;
			}

			.error-title {
				font-size: 2rem;
			}

			.error-message {
				font-size: 1rem;
			}

			.buttons {
				flex-direction: column;
			}
		}
	</style>
</head>

<body>
	<div class="container">
		<h1 class="error-code">404</h1>
		<h2 class="error-title">Page Not Found</h2>
		<p class="error-message">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
		<div class="buttons">
			<a href="/" class="btn btn-primary">Back to Home</a>
		</div>
	</div>
	<canvas class="particles" id="particles"></canvas>

	<script>
		const canvas = document.getElementById('particles');
		const ctx = canvas.getContext('2d');

		canvas.width = window.innerWidth;
		canvas.height = window.innerHeight;

		const particles = [];
		const particleCount = 50;

		class Particle {
			constructor() {
				this.x = Math.random() * canvas.width;
				this.y = Math.random() * canvas.height;
				this.size = Math.random() * 2 + 1;
				this.speedX = Math.random() * 2 - 1;
				this.speedY = Math.random() * 2 - 1;
				this.color = `rgba(255, 255, 255, ${Math.random() * 0.5})`;
			}

			update() {
				this.x += this.speedX;
				this.y += this.speedY;

				if (this.x > canvas.width) this.x = 0;
				if (this.x < 0) this.x = canvas.width;
				if (this.y > canvas.height) this.y = 0;
				if (this.y < 0) this.y = canvas.height;
			}

			draw() {
				ctx.fillStyle = this.color;
				ctx.beginPath();
				ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
				ctx.fill();
			}
		}

		function init() {
			for (let i = 0; i < particleCount; i++) {
				particles.push(new Particle());
			}
		}

		function animate() {
			ctx.clearRect(0, 0, canvas.width, canvas.height);

			particles.forEach(particle => {
				particle.update();
				particle.draw();
			});

			requestAnimationFrame(animate);
		}

		init();
		animate();

		window.addEventListener('resize', () => {
			canvas.width = window.innerWidth;
			canvas.height = window.innerHeight;
		});
	</script>
</body>

</html>