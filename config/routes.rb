Rails.application.routes.draw do
  # One line to define all REST routes for items
  resources :items
  # Oauth callback to add a new user in DB
  get '/auth/slack/callback', to: 'sessions#create'
  
  get '/:team/calendar', to: 'teams#calendar'

  # Show items of a team
  get '/:team', to: 'teams#index'
end
