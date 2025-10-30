'use client';

import * as React from 'react';
import { Moon, Sun, Laptop, Paintbrush } from 'lucide-react';
import { useTheme } from './theme-provider';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
  DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';

export function ThemeToggle() {
  const { setTheme } = useTheme();
  const [isCustomTheme, setIsCustomTheme] = React.useState(false);
  const [colors, setColors] = React.useState(() => {
    if (typeof window !== 'undefined') {
      const savedColors = localStorage.getItem('custom-theme-colors');
      return savedColors ? JSON.parse(savedColors) : { background: '#222222', text: '#ffffff', icon: '#ffffff' };
    }
    return { background: '#222222', text: '#ffffff', icon: '#ffffff' };
  });

  React.useEffect(() => {
    if (typeof window !== 'undefined') {
        const customThemeEnabled = localStorage.getItem('custom-theme-enabled') === 'true';
        setIsCustomTheme(customThemeEnabled);
    }
  }, []);

  React.useEffect(() => {
    const styleId = 'custom-theme-styles';
    let styleTag = document.getElementById(styleId);

    if (isCustomTheme) {
      if (!styleTag) {
        styleTag = document.createElement('style');
        styleTag.id = styleId;
        document.head.appendChild(styleTag);
      }
      styleTag.innerHTML = `
        body {
          background-color: ${colors.background} !important;
          color: ${colors.text} !important;
        }
        .lucide, svg {
          color: ${colors.icon} !important;
        }
      `;
      localStorage.setItem('custom-theme-enabled', 'true');
      localStorage.setItem('custom-theme-colors', JSON.stringify(colors));
    } else {
      if (styleTag) {
        styleTag.remove();
      }
      localStorage.setItem('custom-theme-enabled', 'false');
    }
    
    // Cleanup function to remove style tag if component unmounts
    return () => {
        const styleTag = document.getElementById(styleId);
        if (styleTag) {
            styleTag.remove();
        }
    }
  }, [isCustomTheme, colors]);

  const handleThemeChange = (theme: string) => {
    setIsCustomTheme(false);
    setTheme(theme);
  };
  
  const handleColorChange = (name: keyof typeof colors, value: string) => {
    setColors(prev => ({ ...prev, [name]: value }));
  };

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button variant="ghost" size="icon">
          <Sun className="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
          <Moon className="absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
          <span className="sr-only">Toggle theme</span>
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end">
        <DropdownMenuItem onClick={() => handleThemeChange('light')}>
          <Sun className="mr-2 h-4 w-4" />
          <span>Light</span>
        </DropdownMenuItem>
        <DropdownMenuItem onClick={() => handleThemeChange('dark')}>
          <Moon className="mr-2 h-4 w-4" />
          <span>Dark</span>
        </DropdownMenuItem>
        <DropdownMenuItem onClick={() => handleThemeChange('system')}>
          <Laptop className="mr-2 h-4 w-4" />
          <span>System</span>
        </DropdownMenuItem>
        <DropdownMenuSeparator />
        <div className="p-2 space-y-3">
            <div className="flex items-center justify-between">
                <Label htmlFor="custom-theme-switch" className="flex items-center">
                    <Paintbrush className="mr-2 h-4 w-4" />
                    Custom Theme
                </Label>
                <Switch
                    id="custom-theme-switch"
                    checked={isCustomTheme}
                    onCheckedChange={setIsCustomTheme}
                />
            </div>
            {isCustomTheme && (
                <div className="space-y-2" onClick={(e) => e.stopPropagation()}>
                    <div>
                        <Label htmlFor="background-color">Background</Label>
                        <Input
                            id="background-color"
                            type="color"
                            value={colors.background}
                            onChange={(e) => handleColorChange('background', e.target.value)}
                            className="w-full"
                        />
                    </div>
                    <div>
                        <Label htmlFor="text-color">Text</Label>
                        <Input
                            id="text-color"
                            type="color"
                            value={colors.text}
                            onChange={(e) => handleColorChange('text', e.target.value)}
                            className="w-full"
                        />
                    </div>
                    <div>
                        <Label htmlFor="icon-color">Icon</Label>
                        <Input
                            id="icon-color"
                            type="color"
                            value={colors.icon}
                            onChange={(e) => handleColorChange('icon', e.target.value)}
                            className="w-full"
                        />
                    </div>
                </div>
            )}
        </div>
      </DropdownMenuContent>
    </DropdownMenu>
  );
}
