require 'test_helper'

class TeamTest < ActiveSupport::TestCase
  def setup
    @user = users(:default)
    @team = teams(:default)
  end
  test 'get_token should work' do
    assert_equal @user.token, @team.get_token
  end
end