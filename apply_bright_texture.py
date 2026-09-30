with open('public/assets/paper_bright_b64.txt', 'r') as f:
    b64_bright = f.read().strip()

import re

# Update siswa-layout.blade.php
with open('resources/views/components/siswa-layout.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace .card block with clean bright texture
card_pattern = re.compile(r'\.card\s*\{[^}]*background-image:[^;]*;[^}]*\}', re.DOTALL)

new_card_css = f'''.card {{ 
            background-color: #FFFFFF;
            /* Tekstur kertas cerah & bersih (Base64 instant paint, zero loading delay) */
            background-image: url("{b64_bright}");
            background-repeat: repeat;
            border-radius: 12px; 
            border: 1px solid rgba(0, 0, 0, 0.07);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03), 0 4px 12px rgba(27, 79, 114, 0.03);
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }}'''

content = card_pattern.sub(new_card_css, content, count=1)

with open('resources/views/components/siswa-layout.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print('Updated siswa-layout.blade.php with bright texture!')

# Update bendahara-layout.blade.php
with open('resources/views/components/bendahara-layout.blade.php', 'r', encoding='utf-8') as f:
    b_content = f.read()

b_content = card_pattern.sub(new_card_css, b_content, count=1)

with open('resources/views/components/bendahara-layout.blade.php', 'w', encoding='utf-8') as f:
    f.write(b_content)
print('Updated bendahara-layout.blade.php with bright texture!')
