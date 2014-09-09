class Item < ActiveRecord::Base
  belongs_to :team
  belongs_to :channel
  belongs_to :user
  def self.create_from_webhook(params)
    
    # Get the corresponding team
    team = Team.find_or_create_by(identifier: params[:team_id]) do |t|
      t.name = params[:team_domain]
    end

    channel = Channel.find_or_create_by(identifier: params[:channel_id]) do |c|
      c.name = params[:channel_name]
    end

    user = User.find_or_create_by(identifier: params[:user_id]) do |u|
      u.name = params[:user_name]
    end

    text = params[:text].slice(params[:trigger_word].length..-1).lstrip

    item = Item.create({
      :team => team,
      :channel => channel,
      :user => user,
      :timestamp => params[:timestamp].to_i,
      :text => text
    })
  end
end
