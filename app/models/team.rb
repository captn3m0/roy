class Team < ActiveRecord::Base
  has_many :users
  has_many :channels
  def get_token
    self.users.sample.token
  end
end
