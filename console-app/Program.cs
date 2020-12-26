using System;
using System.Data;
using MySql.Data.MySqlClient; //dotnet add package MySql.Data

namespace console_app
{
    class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("Hello World!");

            string connectionString = @"server=localhost;userid=root;password=;database=result_manager";
            MySqlConnection connection = new MySqlConnection(connectionString);
            
           

           try
           {
               connection.Open();
               calculatePost(connection);
               if(connection.State == ConnectionState.Open) connection.Close();
           }
           catch (Exception exp)
           {
               Console.WriteLine(exp.Message);
               if(connection.State == ConnectionState.Open) connection.Close();
           }
           
        }

        //read quota percentages from post table and convert percentages to quantities in post_calculation table.
        static void calculatePost(MySqlConnection connection)
        {
            DataTable dtPosts = new DataTable();
            using (MySqlCommand command = new MySqlCommand())
            {
                command.Connection = connection;
                command.CommandText = "select * from posts";
                using (MySqlDataAdapter da = new MySqlDataAdapter(command))
                {
                    da.Fill(dtPosts);
                }
            }

            //for each post --->
            foreach (DataRow row in dtPosts.Rows){
                int postId = Convert.ToInt32(row["postId"]);
                int vacancies = Convert.ToInt32(row["vacancies"]);
                int distPercentage = Convert.ToInt32(row["districtQuota"]);
                 int distQuantity = 0;
                if(distPercentage>0){
                    distQuantity = (distPercentage/100)*vacancies;
                   
                }

                int femalePercentage = Convert.ToInt32(row["femaleQuota"]);
                int femaleQuantity = 0;
                if(femalePercentage > 0){
                    femaleQuantity =  (femalePercentage/100)*vacancies;
                }

                int freedomFighterPercentage = Convert.ToInt32(row["freedomFighterQuota"]);
                int freedomFighterQuantity = 0;
                if(freedomFighterPercentage>0){
                    freedomFighterQuantity = (freedomFighterPercentage/100)*vacancies;
                }

                int tribalPercentage = Convert.ToInt32(row["tribalQuota"]);
                int tribalQuantity = 0;
                if(tribalPercentage>0){
                    tribalQuantity = (tribalPercentage/100)*vacancies;
                }

                int generalQuota = vacancies - (distQuantity+femaleQuantity+freedomFighterQuantity+tribalQuantity);

                using (MySqlCommand command = new MySqlCommand())
                {
                    command.Connection = connection;
                    command.CommandText = "insert into post_calculation(postId, distQuantity,femaleQuantity,freedomFighterQuantity, tribalQuantity ) values(" + postId + ", " + distQuantity + "," + femaleQuantity + "," + freedomFighterQuantity + ", " + tribalQuantity + ")";
                    command.ExecuteNonQuery();
                }
            }
        }
   

        //select 1 post which has distQuotaDeclared > distQuotaFound + distQuotaTransferred
        static void distQuotaApplicants(MySqlConnection connection)
        {
            DataTable dt = new DataTable();
            string sql = "select * from post_calculation where distQuotaDeclared > distQuotaFound + distQuotaTransferred order by id limit 1";
            using(MySqlCommand command = new MySqlCommand()){
                command.Connection = connection;
                command.CommandText = sql;
                using(MySqlDataAdapter da = new MySqlDataAdapter(command)){
                    da.Fill(dt);
                }
            }

            if(dt.Rows.Count>0){
                int postId = Convert.ToInt32( dt.Rows[0]["postId"]);
                //now select applicant with this post id
                string applicantSql = "select * from applicants where distQuotaClaimed=yes order by writtenMarks, vivaMarks LIMIT 1";
            }
            else{
                //no post found. Quit here.
            }
        }

    }
}
