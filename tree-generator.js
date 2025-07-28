const fs = require('fs');
const path = require('path');

function generatePHPProjectTree(dir, prefix = '', maxDepth = 4, currentDepth = 0) {
    if (currentDepth >= maxDepth) return '';
    
    const items = fs.readdirSync(dir);
    let result = '';
    
    // Extensiones comunes en proyectos PHP/HTML
    const webExtensions = [
        '.php', '.html', '.htm', '.css', '.js', 
        '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.webp',
        '.json', '.txt', '.md', '.sql'
    ];
    
    // Carpetas que queremos ignorar
    const ignoredItems = ['.git', 'node_modules', '.vscode', 'vendor', '.htaccess'];
    
    const filteredItems = items.filter(item => {
        const itemPath = path.join(dir, item);
        const stats = fs.statSync(itemPath);
        
        if (ignoredItems.includes(item)) return false;
        
        if (stats.isDirectory()) return true;
        
        const ext = path.extname(item).toLowerCase();
        return webExtensions.includes(ext) || item.startsWith('.htaccess');
    });
    
    filteredItems.forEach((item, index) => {
        const itemPath = path.join(dir, item);
        const isLastItem = index === filteredItems.length - 1;
        const stats = fs.statSync(itemPath);
        
        const connector = isLastItem ? '└── ' : '├── ';
        const extension = isLastItem ? '    ' : '│   ';
        
        result += `${prefix}${connector}${item}\n`;
        
        if (stats.isDirectory()) {
            result += generatePHPProjectTree(
                itemPath, 
                prefix + extension, 
                maxDepth, 
                currentDepth + 1
            );
        }
    });
    
    return result;
}

// Información del proyecto
const projectPath = process.argv[2] || '.';
const projectName = path.basename(path.resolve(projectPath));

console.log(`${projectName}/ (Proyecto PHP/HTML)`);
console.log(generatePHPProjectTree(projectPath));

// Estadísticas del proyecto
const stats = getProjectStats(projectPath);
console.log('\nEstadísticas del proyecto:');
console.log(`    Archivos PHP: ${stats.php}`);
console.log(`    Archivos HTML: ${stats.html}`);
console.log(`    Archivos CSS: ${stats.css}`);
console.log(`    Imágenes: ${stats.images}`);
console.log(`    Carpetas: ${stats.folders}`);

// Guardar archivo
const timestamp = new Date().toISOString().split('T')[0];
const filename = `estructura-proyecto-${timestamp}.txt`;
const output = `${projectName}/ (Proyecto PHP/HTML)\n${generatePHPProjectTree(projectPath)}\n\nEstadísticas:\n   PHP: ${stats.php} | HTML: ${stats.html} | CSS: ${stats.css} | Imágenes: ${stats.images}`;

fs.writeFileSync(filename, output);
console.log(`\nEstructura guardada en: ${filename}`);

function getProjectStats(dir) {
    let stats = { php: 0, html: 0, css: 0, images: 0, folders: 0 };
    
    function countFiles(directory) {
        const items = fs.readdirSync(directory);
        
        items.forEach(item => {
            const itemPath = path.join(directory, item);
            const fileStat = fs.statSync(itemPath);
            
            if (fileStat.isDirectory() && !item.startsWith('.')) {
                stats.folders++;
                countFiles(itemPath);
            } else {
                const ext = path.extname(item).toLowerCase();
                switch (ext) {
                    case '.php': stats.php++; break;
                    case '.html':
                    case '.htm': stats.html++; break;
                    case '.css': stats.css++; break;
                    case '.png':
                    case '.jpg':
                    case '.jpeg':
                    case '.gif':
                    case '.svg':
                    case '.webp': stats.images++; break;
                }
            }
        });
    }
    
    countFiles(dir);
    return stats;
}

// Ejecutar con: node tree-generator.js
