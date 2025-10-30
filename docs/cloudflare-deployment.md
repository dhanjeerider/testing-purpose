# 🚀 Cloudflare Pages Deployment Guide

> Complete guide to deploy CineScope to Cloudflare Pages with optimal performance and reliability

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Build Configuration](#build-configuration)
3. [Environment Setup](#environment-setup)
4. [Deployment Steps](#deployment-steps)
5. [Verification](#verification)
6. [Troubleshooting](#troubleshooting)
7. [Performance Optimization](#performance-optimization)
8. [Alternative Methods](#alternative-methods)

---

## ✅ Prerequisites

Before deploying to Cloudflare Pages, ensure you have:

- ✓ A [Cloudflare account](https://dash.cloudflare.com/sign-up)
- ✓ Your GitHub repository connected to Cloudflare
- ✓ A valid [TMDB API key](https://www.themoviedb.org/settings/api)
- ✓ Node.js 18.x or higher installed locally
- ✓ Git configured and repository pushed to GitHub

---

## 🔨 Build Configuration

### Build Command
\`\`\`bash
npm run pages:build
\`\`\`

This command uses `@cloudflare/next-on-pages` to build your Next.js application for Cloudflare Pages.

### Build Output Directory
\`\`\`
.vercel/output/static
\`\`\`

The build process generates static files in this directory that Cloudflare Pages will serve.

### Node Version
- **Recommended**: 18.x or higher
- **Minimum**: 16.x

---

## 🔐 Environment Setup

### Required Environment Variables

Add the following environment variable in your Cloudflare Pages project settings:

| Variable | Value | Required |
|----------|-------|----------|
| `TMDB_API_KEY` | Your TMDB API key | ✓ Yes |

### How to Add Environment Variables

1. Go to your Cloudflare Pages project dashboard
2. Navigate to **Settings** → **Environment variables**
3. Click **Add variable**
4. Enter the variable name and value
5. Click **Save**

### Getting Your TMDB API Key

1. Visit [themoviedb.org/settings/api](https://www.themoviedb.org/settings/api)
2. Create an account or log in
3. Request an API key
4. Copy your API key
5. Add it to Cloudflare Pages environment variables

---

## 🚀 Deployment Steps

### Step 1: Connect Your Repository

1. Go to [Cloudflare Pages Dashboard](https://dash.cloudflare.com/)
2. Click **Pages** in the left sidebar
3. Click **Create a project**
4. Select **Connect to Git**
5. Authorize Cloudflare to access your GitHub account
6. Select your repository

### Step 2: Configure Build Settings

1. **Project name**: Enter your project name (e.g., `cinescope`)
2. **Production branch**: Select `main` (or your default branch)
3. **Build command**: Enter `npm run pages:build`
4. **Build output directory**: Enter `.vercel/output/static`
5. **Root directory**: Leave empty (unless your project is in a subdirectory)

### Step 3: Add Environment Variables

1. Click **Environment variables**
2. Add `TMDB_API_KEY` with your API key value
3. Make sure it's set for **Production** environment

### Step 4: Deploy

1. Click **Save and Deploy**
2. Wait for the build to complete (usually 2-5 minutes)
3. Once complete, you'll receive a deployment URL

### Step 5: Verify Deployment

1. Click the deployment URL to visit your site
2. Test key features:
   - Homepage loads correctly
   - Search functionality works
   - Movie/TV show pages load
   - Images display properly

---

## ✔️ Verification Checklist

After deployment, verify the following:

- [ ] Homepage loads without errors
- [ ] Navigation works correctly
- [ ] Search functionality is operational
- [ ] Movie and TV show detail pages load
- [ ] Images are displayed properly
- [ ] Responsive design works on mobile
- [ ] No console errors in browser DevTools
- [ ] API calls are successful (check Network tab)

---

## 🔧 Troubleshooting

### Build Fails with "Routes not configured for Edge Runtime"

**Problem**: Build fails with error about Edge Runtime configuration

**Solution**:
\`\`\`typescript
// Add this to the top of dynamic route files
export const runtime = 'edge';
\`\`\`

All dynamic routes in this application already have this configured.

### Build Fails with "Module not found"

**Problem**: Build fails with missing module errors

**Solution**:
1. Ensure all dependencies are in `package.json`
2. Run `npm install` locally to verify
3. Check for typos in import statements
4. Clear Cloudflare cache and rebuild

### API Calls Failing (404 or 401 errors)

**Problem**: TMDB API calls return errors

**Solution**:
1. Verify `TMDB_API_KEY` is set in Cloudflare environment variables
2. Check that the API key is valid and not expired
3. Ensure the API key has the correct permissions
4. Check TMDB API status at [status.themoviedb.org](https://status.themoviedb.org)

### Images Not Loading

**Problem**: Images appear broken or don't load

**Solution**:
1. Images are configured as unoptimized for Cloudflare compatibility
2. Check that image URLs are correct
3. Verify CORS headers are properly configured
4. Check browser console for specific errors

### Version Conflicts

**Problem**: Build fails due to version conflicts

**Solution**:
- Next.js version: 15.5.2 or compatible
- @cloudflare/next-on-pages: 1.13.16 or higher
- Node.js: 18.x or higher

Run locally to verify:
\`\`\`bash
npm run build
npm run pages:build
\`\`\`

### Slow Build Times

**Problem**: Build takes longer than expected

**Solution**:
1. Cloudflare Pages builds are typically 2-5 minutes
2. First build may be slower due to dependency installation
3. Subsequent builds are faster due to caching
4. Check Cloudflare status page for any issues

---

## ⚡ Performance Optimization

### Recommended Optimizations

1. **Enable Cloudflare Caching**
   - Go to **Caching** settings
   - Set cache level to "Cache Everything"
   - Set browser cache TTL to 1 hour

2. **Enable Compression**
   - Go to **Speed** settings
   - Enable Brotli compression
   - Enable Gzip compression

3. **Enable HTTP/2 Push**
   - Go to **Network** settings
   - Enable HTTP/2 Server Push

4. **Set Security Headers**
   - Go to **Security** settings
   - Enable HSTS
   - Set appropriate CSP headers

### Monitoring Performance

1. Go to **Analytics** in your Cloudflare Pages project
2. Monitor:
   - Page load times
   - Request counts
   - Error rates
   - Bandwidth usage

---

## 🔄 Alternative Methods

### Using Wrangler CLI

Deploy directly using Wrangler CLI:

\`\`\`bash
# Install Wrangler globally
npm install -g wrangler

# Build the project
npm run pages:build

# Deploy to Cloudflare Pages
wrangler pages deploy .vercel/output/static
\`\`\`

### Using GitHub Actions

Create `.github/workflows/deploy.yml`:

\`\`\`yaml
name: Deploy to Cloudflare Pages

on:
  push:
    branches:
      - main

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '18'
      
      - name: Install dependencies
        run: npm install
      
      - name: Build
        run: npm run pages:build
      
      - name: Deploy to Cloudflare Pages
        uses: cloudflare/pages-action@v1
        with:
          apiToken: ${{ secrets.CLOUDFLARE_API_TOKEN }}
          accountId: ${{ secrets.CLOUDFLARE_ACCOUNT_ID }}
          projectName: cinescope
          directory: .vercel/output/static
\`\`\`

---

## 📚 Additional Resources

- [Cloudflare Pages Documentation](https://developers.cloudflare.com/pages/)
- [Next.js on Cloudflare](https://developers.cloudflare.com/pages/framework-guides/nextjs/)
- [@cloudflare/next-on-pages](https://github.com/cloudflare/next-on-pages)
- [TMDB API Documentation](https://developer.themoviedb.org/docs)
- [Wrangler CLI Documentation](https://developers.cloudflare.com/workers/wrangler/)

---

## 🆘 Getting Help

If you encounter issues:

1. **Check the troubleshooting section** above
2. **Review Cloudflare Pages logs** in your dashboard
3. **Check browser console** for client-side errors
4. **Verify environment variables** are set correctly
5. **Test locally** with `npm run dev` to isolate issues
6. **Contact Cloudflare Support** for infrastructure issues

---

## 📝 Deployment Checklist

Before deploying to production:

- [ ] All environment variables are set
- [ ] Build command is correct: `npm run pages:build`
- [ ] Output directory is correct: `.vercel/output/static`
- [ ] Node version is 18.x or higher
- [ ] Repository is pushed to GitHub
- [ ] TMDB API key is valid and active
- [ ] Local build succeeds: `npm run pages:build`
- [ ] No console errors in development
- [ ] All features tested locally

---

<div align="center">

**Happy Deploying! 🎉**

[⬆ back to top](#-cloudflare-pages-deployment-guide)

</div>
