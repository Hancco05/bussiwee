<!-- Pestaña Apariencia -->
<div id="tab-apariencia" class="config-content">
    <h2>🎨 Configuración de Apariencia</h2>
    
    <?php
    // Incluir el theme manager
    require_once 'theme_manager.php';
    $themeManager = new ThemeManager($_SESSION['user_id']);
    $themeConfig = $themeManager->getThemeConfig();
    
    // Procesar actualización de tema
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_tema'])) {
        $tema = $_POST['tema'];
        $color_primario = $_POST['color_primario'];
        $densidad = $_POST['densidad'];
        
        // Validar que los valores no estén vacíos
        if (empty($color_primario)) {
            $color_primario = $themeConfig['color_primario'];
        }
        if (empty($densidad)) {
            $densidad = $themeConfig['densidad'];
        }
        
        if ($themeManager->updateTheme($tema, $color_primario, $densidad)) {
            $mensaje_tema = "✅ Configuración de apariencia guardada exitosamente";
            // Recargar configuración
            $themeConfig = $themeManager->getThemeConfig();
            
            // Recargar la página para aplicar los cambios
            echo "<script>setTimeout(function() { location.reload(); }, 1000);</script>";
        } else {
            $error_tema = "❌ Error al guardar la configuración de apariencia";
        }
    }
    ?>
    
    <?php if (isset($mensaje_tema)): ?>
        <div class="success"><?php echo $mensaje_tema; ?></div>
    <?php endif; ?>
    <?php if (isset($error_tema)): ?>
        <div class="error"><?php echo $error_tema; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="config-section">
            <h3>🌙 Modo de Tema</h3>
            <div class="theme-selector">
                <div class="theme-options">
                    <label class="theme-option">
                        <input type="radio" name="tema" value="claro" <?php echo $themeConfig['tema'] == 'claro' ? 'checked' : ''; ?>>
                        <div class="theme-preview claro">
                            <div class="preview-header"></div>
                            <div class="preview-content">
                                <div class="preview-card"></div>
                                <div class="preview-card"></div>
                            </div>
                        </div>
                        <span>Claro</span>
                    </label>
                    
                    <label class="theme-option">
                        <input type="radio" name="tema" value="oscuro" <?php echo $themeConfig['tema'] == 'oscuro' ? 'checked' : ''; ?>>
                        <div class="theme-preview oscuro">
                            <div class="preview-header"></div>
                            <div class="preview-content">
                                <div class="preview-card"></div>
                                <div class="preview-card"></div>
                            </div>
                        </div>
                        <span>Oscuro</span>
                    </label>
                    
                    <label class="theme-option">
                        <input type="radio" name="tema" value="auto" <?php echo $themeConfig['tema'] == 'auto' ? 'checked' : ''; ?>>
                        <div class="theme-preview auto">
                            <div class="preview-header"></div>
                            <div class="preview-content">
                                <div class="preview-card"></div>
                                <div class="preview-card"></div>
                            </div>
                        </div>
                        <span>Automático</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="config-section">
            <h3>🎨 Color Primario</h3>
            <div class="color-picker">
                <div class="color-options">
                    <?php
                    $colores = [
                        '#007bff' => 'Azul',
                        '#28a745' => 'Verde', 
                        '#dc3545' => 'Rojo',
                        '#ffc107' => 'Amarillo',
                        '#6f42c1' => 'Púrpura',
                        '#fd7e14' => 'Naranja'
                    ];
                    
                    foreach ($colores as $color => $nombre):
                    ?>
                    <label class="color-option">
                        <input type="radio" name="color_primario" value="<?php echo $color; ?>" 
                               <?php echo $themeConfig['color_primario'] == $color ? 'checked' : ''; ?>>
                        <div class="color-swatch" style="background: <?php echo $color; ?>"></div>
                        <span><?php echo $nombre; ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="config-section">
            <h3>📐 Densidad de Interfaz</h3>
            <div class="density-selector">
                <div class="density-options">
                    <label class="density-option">
                        <input type="radio" name="densidad" value="compacto" <?php echo $themeConfig['densidad'] == 'compacto' ? 'checked' : ''; ?>>
                        <div class="density-preview compacto">
                            <div class="density-line short"></div>
                            <div class="density-line short"></div>
                            <div class="density-line short"></div>
                        </div>
                        <span>Compacto</span>
                    </label>
                    
                    <label class="density-option">
                        <input type="radio" name="densidad" value="comodo" <?php echo $themeConfig['densidad'] == 'comodo' ? 'checked' : ''; ?>>
                        <div class="density-preview comodo">
                            <div class="density-line medium"></div>
                            <div class="density-line medium"></div>
                            <div class="density-line medium"></div>
                        </div>
                        <span>Cómodo</span>
                    </label>
                    
                    <label class="density-option">
                        <input type="radio" name="densidad" value="espaciado" <?php echo $themeConfig['densidad'] == 'espaciado' ? 'checked' : ''; ?>>
                        <div class="density-preview espaciado">
                            <div class="density-line long"></div>
                            <div class="density-line long"></div>
                            <div class="density-line long"></div>
                        </div>
                        <span>Espaciado</span>
                    </label>
                </div>
            </div>
        </div>

        <button type="submit" name="guardar_tema" class="btn">💾 Guardar Apariencia</button>
    </form>

    <!-- Vista Previa en Tiempo Real -->
    <div class="config-section">
        <h3>👀 Vista Previa</h3>
        <div class="theme-preview-live <?php echo $themeManager->getThemeClass(); ?>" 
             style="--accent-color: <?php echo $themeConfig['color_primario']; ?>">
            <div class="preview-header">
                <h4>Panel de Vista Previa</h4>
                <button class="preview-btn">Acción</button>
            </div>
            <div class="preview-content">
                <div class="preview-card">
                    <h5>Tarjeta de Ejemplo</h5>
                    <p>Este es un texto de ejemplo en la vista previa.</p>
                    <div class="preview-stats">
                        <span class="stat">📊 1,234</span>
                        <span class="stat">✅ 56%</span>
                    </div>
                </div>
                <div class="preview-card">
                    <h5>Otra Tarjeta</h5>
                    <p>Más contenido de ejemplo para mostrar el tema.</p>
                </div>
            </div>
        </div>
    </div>
</div>