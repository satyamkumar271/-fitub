<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $allPosts = $this->getDummyPosts();

        // Data ko alag alag karna behtar practice hai
        $featuredPost = collect($allPosts)->firstWhere('featured', true);
        $regularPosts = collect($allPosts)->where('featured', '!=', true);

        return view('blog.index', [
            'featuredPost' => $featuredPost,
            'regularPosts' => $regularPosts,
            // Hum sabhi posts bhi bhejenge taaki sidebar unhe use kar sake
            'allPosts' => $allPosts
        ]);
    }

    // Is private function mein full content bhi add kar dein
    private function getDummyPosts(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'The Ultimate Guide to a Healthy Diet for Gym Goers',
                'slug' => 'ultimate-guide-healthy-diet',
                'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=1780&auto=format&fit=crop',
                'category' => 'Nutrition',
                'excerpt' => 'A balanced diet is crucial for maximizing your gym performance. Learn about macros, micros, and the best foods to fuel your body...',
                'content' => '<h2>What are Macronutrients?</h2><p>Macronutrients are the nutrients we use in the largest amounts. They include carbohydrates, proteins, and fats. A proper balance is essential for energy, muscle repair, and overall health.</p><h3>Carbohydrates</h3><p>They are your body\'s main source of energy. Opt for complex carbs like oats, brown rice, and sweet potatoes.</p><h3>Protein</h3><p>Crucial for muscle repair and growth. Aim for sources like chicken, fish, eggs, and legumes.</p>',
                'author' => 'Dr. Priya Sharma',
                'date' => 'October 26, 2023',
                'read_time' => '7 min read',
                'featured' => true,
            ],
            [
                'id' => 2,
                'title' => '5 Common Workout Mistakes and How to Avoid Them',
                'slug' => '5-common-workout-mistakes',
                'image' => 'https://images.unsplash.com/photo-1599058917212-d750089bc07e?q=80&w=1769&auto=format&fit=crop',
                'category' => 'Workout',
                'excerpt' => 'Are you making these common mistakes at the gym? From improper form to neglecting rest, we cover what to avoid for better results.',
                'content' => '<h2>Mistake #1: Ego Lifting</h2><p>Lifting too heavy with poor form is a recipe for disaster. Focus on proper technique before increasing weight.</p><h2>Mistake #2: Skipping Warm-ups</h2><p>A good warm-up prepares your muscles for exercise and reduces the risk of injury. Don\'t skip it!</p>',
                'author' => 'Rohan Verma',
                'date' => 'October 22, 2023',
                'read_time' => '5 min read',
                'featured' => false,
            ],
            [
                'id' => 3,
                'title' => 'Mindfulness and Fitness: The Mental Aspect of Training',
                'slug' => 'mindfulness-and-fitness',
                'image' => 'https://images.unsplash.com/photo-1506126613408-4e0520d380b0?q=80&w=1770&auto=format&fit=crop',
                'category' => 'Wellness',
                'excerpt' => 'Fitness is not just physical. Discover how practicing mindfulness can reduce stress, improve focus, and enhance your connection with your body.',
                'content' => '<p>Fitness is not just physical. Discover how practicing mindfulness can reduce stress, improve focus, and enhance your connection with your body during workouts. Techniques like deep breathing and body scans can transform your training sessions.</p>',
                'author' => 'Anjali Mehta',
                'date' => 'October 18, 2023',
                'read_time' => '6 min read',
                'featured' => false,
            ],
        ];
    }
}
