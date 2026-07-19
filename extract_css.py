import re

with open('resources/views/welcome.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

styles = re.findall(r'<style>(.*?)</style>', content, re.DOTALL)
css_content = "\n".join(styles)

# Remove all style blocks
new_content = re.sub(r'<style>.*?</style>', '', content, flags=re.DOTALL)

# Add the link tag where the first style block used to be (roughly around </head>)
# We can just inject it right before </head>
new_content = new_content.replace('</head>', '    <link rel="stylesheet" href="{{ asset(\'css/welcome.css\') }}">\n</head>')

with open('public/css/welcome.css', 'w', encoding='utf-8') as f:
    f.write(css_content)

with open('resources/views/welcome.blade.php', 'w', encoding='utf-8') as f:
    f.write(new_content)

print("CSS extracted successfully!")
