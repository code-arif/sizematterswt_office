# 🌾 Farm & Ranch Navigation API

A Laravel-based REST API for a **Farm & Ranch Navigation Mobile Application**.  
This platform works like **Google Maps for Farms & Ranches**, helping users discover locations, navigate quickly, track visits, and view relevant farm-related advertisements.

## 📌 Project Overview

The **Farm & Ranch Navigation App** allows users to:

- Discover farms & ranches on an interactive map
- Get the fastest navigation route
- Track visited places
- Call farms/ranches directly
- View targeted farm-related advertisements
- Subscribe yearly for full access

This repository contains the **Laravel backend REST API** that powers the mobile application.

## 👥 User Roles

### 🧑‍💼 Admin

- Add and manage Farms & Ranches
- Upload advertisements
- Manage user subscriptions
- Manage users
- Control active/inactive status of content

### 👤 User

- View farms & ranches on map
- Navigate to selected locations
- Mark places as visited
- Call farm/ranch directly
- View relevant advertisements
- Subscribe yearly for access

## 🧭 Core Features

### 🗺️ Navigation System

- Show fastest route to farm/ranch
- GPS-based real-time navigation
- Map integration ready

### 🎨 Color Map Indicators

| Color        | Meaning |
| ------------ | ------- |
| 🟢 **Green** | Farm    |
| 🟤 **Brown** | Ranch   |
| 🔴 **Red**   | Visited |

### 📍 Visited Tracking

- Mark farm/ranch as visited
- Store visit history
- Visited locations shown in red on map

### 📞 Click to Call

- Direct call to farm/ranch phone number
- Mobile-optimized API responses

### 📢 Advertisement System

Admins can upload ads for:

- Fertilizer
- Animal feed
- Tractors
- Local farm/ranch events

Users see **location-based and relevant ads** within map radius.

### 💳 Subscription System

- Yearly subscription required (Example: **$49.99 / year**)
- Only subscribed users can access map data

**Features:**

- Subscription check middleware
- Expiry validation
- Active subscription API endpoint

## 🚀 How It Works

1. **Admin Adds Data**  
   Admin adds Farms/Ranches with name, address, lat/long, phone, and images.

2. **User Subscribes**  
   User creates account → Pays yearly subscription → Gains full access.

3. **Navigation**  
   User opens map → Selects location → Gets fastest route.

4. **Mark Visited**  
   After visiting, user marks it as visited (turns red on map).

5. **Advertisements**  
   Relevant local and farm-related ads shown based on user location.

## 🏗️ Tech Stack

- **Backend**: Laravel 10+
- **Database**: MySQL
- **Authentication**: Laravel Sanctum (Recommended)
- **API**: RESTful Architecture

### Key Features Implemented

- API Authentication & Role-based Access
- Media Upload Support
- Subscription Management
- Visited Places Tracking
- Location-based Ads

## 📡 API Modules

- **Authentication**
    - Register, Login, Logout, Profile

- **Farms**
    - List farms, Show farm, Add/Update/Delete (Admin only)

- **Ranches**
    - List ranches, Show ranch, Add/Update/Delete (Admin only)

- **Visited**
    - Mark as visited, Get visited list

- **Advertisements**
    - List ads, Nearby ads, Upload ads (Admin)

- **Subscription**
    - Subscribe, Check subscription status, Expiry validation

## 📂 Project Structure

```bash
app/
├── Http/
│   ├── Controllers/API/
│   ├── Resources/
│   └── Requests/
├── Models/
├── Services/
├── Traits/
│   └── ApiResponse.php
```
