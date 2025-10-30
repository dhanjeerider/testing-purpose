import type React from "react"
import type { Metadata, Viewport } from "next"
import { ThemeProvider } from "@/components/layout/theme-provider"
import { Header } from "@/components/layout/header"
import { Footer } from "@/components/layout/footer"
import { MobileBottomNav } from "@/components/layout/mobile-bottom-nav"
import { Toaster } from "@/components/ui/toaster"
import "./globals.css"

export const metadata: Metadata = {
  title: {
    default: "Vega Movies",
    template: "%s | Vega Movies",
  },
  description: "A modern movie and TV show explorer built with Next.js and TMDB.",
  authors: [{ name: "dhanjee rider" }],
  robots: "all",
}

export const viewport: Viewport = {
  themeColor: "violet",
}

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode
}>) {
  return (
    <html lang="en" suppressHydrationWarning>
      <head>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="anonymous" />
        <link
          href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap"
          rel="stylesheet"
        />
      </head>
      <body className="font-body antialiased">
        <ThemeProvider defaultTheme="dark" storageKey="vegamovies-theme">
          <div className="relative min-h-dvh flex-col bg-background">
            <Header />
            <main className="flex-1">{children}</main>
            <Footer />
            <MobileBottomNav />
            <Toaster />
          </div>
        </ThemeProvider>
      </body>
    </html>
  )
}
