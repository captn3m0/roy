class SessionsController < ApplicationController
  def create
    user = User.create_from_oauth(auth_hash)
    session[:team] = session[:team].to_set || Set.new
    session[:team].add user.team
    session[:user] = user
    redirect_to "/#{user.team.name}"
  end

  protected
  def auth_hash
    request.env['omniauth.auth']
  end
end