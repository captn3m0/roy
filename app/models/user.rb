class User < ActiveRecord::Base
  belongs_to :team
  def self.create_from_oauth(auth_hash)
    # Create a team as well
    team = Team.find_or_create_by(:identifier=> auth_hash['info']['team_id']) do |t|
      # Strip out the domain from the url
      t.name = auth_hash['extra']['raw_info']['url'][/https:\/\/([\w-]+)\.slack\.com/,1]
    end
    user = User.find_or_create_by(:identifier=> auth_hash['uid']) do |u|
      u.name = auth_hash['info']['user']
      u.team = team
    end

    user.update_attribute('token', auth_hash['credentials']['token'])
    user
  end
end
