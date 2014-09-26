Rails.application.routes.draw do
  # One line to define all REST routes for items
  resources :items
  # Oauth callback to add a new user in DB
  get '/auth/slack/callback', to: 'sessions#create'
end
